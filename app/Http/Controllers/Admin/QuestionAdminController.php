<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AnswerSubmittedMail;
use App\Mail\FatwaPublishedMail;
use App\Mail\QuestionAssignedMail;
use App\Models\Category;
use App\Models\Fatwa;
use App\Models\FatwaActivityLog;
use App\Models\MuftiAnswer;
use App\Models\Question;
use App\Models\SupportingReference;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class QuestionAdminController extends Controller
{
    /** List all questions with filters */
    public function index(Request $request)
    {
        $query = Question::with('user', 'category', 'assignedMufti')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('madhab')) {
            $query->where('madhab', $request->madhab);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('question_id', 'like', '%' . $request->search . '%')
                  ->orWhere('body', 'like', '%' . $request->search . '%');
            });
        }

        $questions = $query->paginate(20)->withQueryString();
        $muftis    = User::role('mufti')->get();
        return view('admin.questions.index', compact('questions', 'muftis'));
    }

    /** Show a single question detail */
    public function show(Question $question)
    {
        $question->load('user', 'category', 'files', 'assignedMufti', 'muftiAnswer.supportingReferences');
        $muftis         = User::role('mufti')->get();
        $mainCategories = Category::whereNull('parent_id')->get();
        $subCategories  = Category::whereNotNull('parent_id')->get();
        
        return view('admin.questions.show', compact('question', 'muftis', 'mainCategories', 'subCategories'));
    }

    /** Full fatwa history / timeline */
    public function history(Question $question)
    {
        $question->load([
            'user', 'category', 'assignedMufti',
            'muftiAnswer.mufti',
            'fatwa.publisher', 'fatwa.verifiedBy', 'fatwa.checkedApprovedBy',
            'fatwa.userViews.user',
        ]);

        // DB activity logs
        $logs = \App\Models\FatwaActivityLog::where('question_id', $question->id)
            ->with('actor')
            ->orderBy('event_at')
            ->get();

        // 45-day view snapshots (if fatwa exists)
        $snapshots = [];
        if ($question->fatwa) {
            $snapshots = \App\Models\FatwaViewSnapshot::where('fatwa_id', $question->fatwa->id)
                ->orderBy('snapshot_date')
                ->get();
        }

        // Questioner's personal view history from fatwa_user_views
        $userViews = $question->fatwa
            ? \App\Models\FatwaUserView::where('fatwa_id', $question->fatwa->id)
                ->where('user_id', $question->user_id)
                ->first()
            : null;

        return view('admin.questions.history', compact('question', 'logs', 'snapshots', 'userViews'));
    }

    /** Accept and update question details */
    public function accept(Request $request, Question $question)
    {
        $request->validate([
            'title'       => ['required', 'string', 'max:200'],
            'language'    => ['required', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
        ]);

        $question->update([
            'status'      => 'accepted',
            'title'       => $request->title,
            'language'    => $request->language,
            'category_id' => $request->category_id,
            'accepted_at' => now(),
        ]);

        FatwaActivityLog::log(
            $question->id, 'accepted', 'Question Accepted & Categorised',
            'bi-check-circle', 'success',
            'Categorised under: ' . optional($question->fresh()->category)->name, Auth::id()
        );

        return redirect()->route('admin.questions.show', $question)->with('success', 'Question accepted and categorized successfully.');
    }

    /** Assign question to a Mufti */
    public function assign(Request $request, Question $question)
    {
        $request->validate([
            'mufti_id' => ['required', 'exists:users,id'],
        ]);

        $mufti = User::findOrFail($request->mufti_id);
        $question->update([
            'assigned_mufti_id' => $mufti->id,
            'status'            => 'assigned',
            'assigned_at'       => now(),
        ]);

        FatwaActivityLog::log(
            $question->id, 'assigned', 'Assigned to Mufti',
            'bi-person-check', 'primary',
            'Assigned to: ' . $mufti->name, Auth::id()
        );

        Mail::to($mufti->email)->send(new QuestionAssignedMail($question));

        return redirect()->route('admin.questions.show', $question)->with('success', 'Question assigned to ' . $mufti->name);
    }

    /** Admin writes or edits answer on behalf of Mufti */
    public function createAnswer(Question $question)
    {
        $question->load('user', 'files', 'muftiAnswer.supportingReferences');
        $muftis = User::role('mufti')->get();
        return view('admin.questions.answer', compact('question', 'muftis'));
    }

    public function storeAnswer(Request $request, Question $question)
    {
        $request->validate([
            'mufti_id'         => ['required', 'exists:users,id'],
            'summary'          => ['required', 'string'],
            'full_answer'      => ['required', 'string'],
            'language'         => ['nullable', 'string', 'in:English,Arabic,Urdu,Malayalam,Hindi'],
            'arabic_text.*'    => ['nullable', 'string'],
            'book_name.*'      => ['nullable', 'string'],
            'ref_language.*'   => ['nullable', 'string'],
        ]);

        // Update the question language if provided
        if ($request->filled('language')) {
            $question->update(['language' => $request->language]);
        }

        // Find existing answer or create new one
        $answer = MuftiAnswer::updateOrCreate(
            ['question_id' => $question->id],
            [
                'mufti_id'     => $request->mufti_id,
                'summary'      => $request->summary,
                'full_answer'  => $request->full_answer,
                'status'       => $question->status === 'published' ? 'submitted' : 'submitted', // Keep as submitted for review if not already published
                'submitted_at' => now(),
            ]
        );

        // If it was already published, we keep it approved but update the answer
        if ($question->status === 'published') {
            $answer->update(['status' => 'approved']);
            
            // Handle featured image update for published fatwa
            if ($request->filled('featured_image_cropped')) {
                $imagePath = $this->saveCroppedImage($request->featured_image_cropped);
                if ($imagePath && $question->fatwa) {
                    $question->fatwa->update(['featured_image' => $imagePath]);
                }
            }
        }

        // Save supporting references (clean and recreate for simplicity)
        $answer->supportingReferences()->delete();
        
        if ($request->filled('arabic_text')) {
            foreach ($request->arabic_text as $i => $text) {
                if ($text) {
                    SupportingReference::create([
                        'mufti_answer_id' => $answer->id,
                        'arabic_text'     => $text,
                        'book_name'       => $request->book_name[$i] ?? '',
                        'language'        => $request->ref_language[$i] ?? 'Arabic',
                        'sort_order'      => $i,
                    ]);
                }
            }
        }

        if ($question->status !== 'published') {
            $question->update(['status' => 'answered']);

            FatwaActivityLog::log(
                $question->id, 'answer_submitted', 'Answer Written & Submitted for Review',
                'bi-pencil-square', 'info',
                'Written by: ' . (User::find($request->mufti_id)?->name ?? 'Admin'),
                Auth::id()
            );

            return redirect()->route('admin.questions.verify', $question)->with('success', 'Answer saved. Now verify and publish.');
        }

        FatwaActivityLog::log(
            $question->id, 'answer_updated', 'Published Answer Updated',
            'bi-pencil', 'warning',
            'Answer edited after publishing.', Auth::id()
        );

        return redirect()->route('admin.questions.show', $question)->with('success', 'Published answer updated successfully.');
    }

    /** Admin verifies Mufti's answer */
    public function verify(Question $question)
    {
        $question->load('muftiAnswer.mufti', 'muftiAnswer.supportingReferences', 'user');

        $writtenByMuftiId = $question->muftiAnswer->mufti_id;

        // All muftis except the one who wrote the answer (for 'Verified by')
        $verifierMuftis = User::role('mufti')
            ->where('id', '!=', $writtenByMuftiId)
            ->get();

        // Only muftis with can_check_approve permission (for 'Checked & Approved by')
        $approverMuftis = User::role('mufti')
            ->where('can_check_approve', true)
            ->get();

        return view('admin.fatwas.verify', compact('question', 'verifierMuftis', 'approverMuftis'));
    }

    public function approve(Request $request, Question $question)
    {
        $request->validate([
            'verified_by'          => ['nullable', 'exists:users,id'],
            'checked_approved_by'  => ['nullable', 'exists:users,id'],
        ]);

        // Ensure Question Title (Summary) is present before publishing
        if (!$question->title || strlen($question->title) > 200) {
            return redirect()->back()->withErrors(['title' => 'A Question Summary (max 200 chars) is mandatory for publishing. Please edit the question details first.']);
        }

        $answer = $question->muftiAnswer;
        $answer->update([
            'status'      => 'approved',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        $featuredImagePath = null;
        if ($request->filled('featured_image_cropped')) {
            $featuredImagePath = $this->saveCroppedImage($request->featured_image_cropped);
        } elseif ($request->hasFile('featured_image')) {
            $image = $request->file('featured_image');
            $filename = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/fatwas'), $filename);
            $featuredImagePath = 'uploads/fatwas/' . $filename;
        }

        $fatwa = Fatwa::create([
            'question_id'         => $question->id,
            'mufti_answer_id'     => $answer->id,
            'published_by'        => Auth::id(),
            'verified_by'         => $request->verified_by,
            'checked_approved_by' => $request->checked_approved_by,
            'featured_image'      => $featuredImagePath,
            'published_at'        => now(),
        ]);

        $question->update(['status' => 'published']);

        FatwaActivityLog::log(
            $question->id, 'published', 'Fatwa Published Publicly',
            'bi-patch-check-fill', 'success',
            'Published by: ' . Auth::user()->name .
            ($request->verified_by ? ' | Verified by: ' . optional(User::find($request->verified_by))->name : '') .
            ($request->checked_approved_by ? ' | Approved by: ' . optional(User::find($request->checked_approved_by))->name : ''),
            Auth::id(), $fatwa->id
        );

        // Notify questioner
        Mail::to($question->user->email)->send(new FatwaPublishedMail($fatwa));

        return redirect()->route('admin.questions.index')->with('success', 'Fatwa published successfully!');
    }

    public function reject(Request $request, Question $question)
    {
        $request->validate(['admin_notes' => 'required|string']);
        $question->muftiAnswer->update([
            'status'      => 'rejected',
            'admin_notes' => $request->admin_notes,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);
        $question->update(['status' => 'under_review']);

        FatwaActivityLog::log(
            $question->id, 'rejected', 'Answer Rejected — Returned to Mufti',
            'bi-x-circle', 'danger',
            'Admin notes: ' . $request->admin_notes, Auth::id()
        );

        return redirect()->back()->with('success', 'Answer rejected with notes.');
    }

    public function requestCorrection(Request $request, Question $question)
    {
        $request->validate(['admin_notes' => 'required|string']);
        $question->muftiAnswer->update([
            'status'      => 'correction_requested',
            'admin_notes' => $request->admin_notes,
        ]);
        $question->update(['status' => 'under_review']);

        FatwaActivityLog::log(
            $question->id, 'correction_requested', 'Correction Requested from Mufti',
            'bi-arrow-repeat', 'warning',
            'Admin notes: ' . $request->admin_notes, Auth::id()
        );

        return redirect()->back()->with('success', 'Correction requested from Mufti.');
    }

    /** Download question file */
    public function downloadFile(\App\Models\QuestionFile $file)
    {
        return Storage::disk('local')->download($file->file_path, $file->original_name);
    }
    /** Helper to decode base64 cropped image and save to disk */
    private function saveCroppedImage($base64Data)
    {
        try {
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $type)) {
                $data = substr($base64Data, strpos($base64Data, ',') + 1);
                $type = strtolower($type[1]); // jpg, png, etc
                $data = base64_decode($data);

                if ($data === false) return null;

                $filename = 'fatwa_' . time() . '_' . \Illuminate\Support\Str::random(5) . '.' . $type;
                $directory = public_path('uploads/fatwas');
                
                if (!file_exists($directory)) {
                    mkdir($directory, 0755, true);
                }

                file_put_contents($directory . '/' . $filename, $data);
                return 'uploads/fatwas/' . $filename;
            }
        } catch (\Exception $e) {
            \Log::error("Cropped Image upload error: " . $e->getMessage());
        }
        return null;
    }
}
