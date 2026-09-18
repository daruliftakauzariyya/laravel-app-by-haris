<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\FatwaPublishedMail;
use App\Mail\QuestionAssignedMail;
use App\Models\Category;
use App\Models\QuestionAndAnswer;
use App\Models\Reference;
use App\Models\FatwaActivityLog;
use App\Models\User;
use App\Models\QuestionStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class QuestionAdminController extends Controller
{
    /** List all questions with filters */
    public function index(Request $request)
    {
        $query = QuestionAndAnswer::with('user', 'category', 'assignedMufti', 'madhab')->latest();

        if ($request->filled('status')) {
            if ($request->status === 'drafted' || $request->status === 'draft') {
                $query->where(function ($q) {
                    $q->whereNotNull('draft_answer')
                      ->where('draft_answer', '!=', '')
                      ->whereNotIn('status', ['published', 'rejected']);
                });
            } else {
                $query->where('status', $request->status);
            }
        }
        if ($request->filled('madhab_id')) {
            $query->where('madhab_id', $request->madhab_id);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('question_id', 'like', '%' . $request->search . '%')
                  ->orWhere('body', 'like', '%' . $request->search . '%');
            });
        }

        $questions = $query->paginate(20)->withQueryString();
        $muftis    = User::whereHas('role', fn($q) => $q->where('name', 'mufti'))->get();
        return view('admin.questions.index', compact('questions', 'muftis'));
    }

    public function show(QuestionAndAnswer $question)
    {
        $question->load([
            'user', 'category', 'assignedMufti', 
            'references',
            'statusHistory' => fn($q) => $q->with('actor')->orderBy('created_at', 'desc')
        ]);

        if (Auth::check()) {
            $adminOpenedKey = 'admin_opened_q_' . $question->id . '_' . Auth::id();
            if (!session()->has($adminOpenedKey)) {
                session()->put($adminOpenedKey, true);
                FatwaActivityLog::log(
                    $question->id, 'admin_opened',
                    'Inquiry ' . $question->question_id . ' Opened by Admin ' . Auth::user()->name,
                    'bi-eye-fill', 'primary',
                    'Admin ' . Auth::user()->name . ' opened and reviewed inquiry details @ ' . now()->format('Y-m-d H:i:s'),
                    Auth::id()
                );
            }
        }

        $logs = FatwaActivityLog::getComprehensiveTimeline($question);

        $madhab = $question->madhab;
        $muftis = User::whereHas('role', fn($q) => $q->where('name', 'mufti'))
            ->where('is_active', true)
            ->with('madhab')
            ->get()
            ->sortBy(function ($m) use ($question) {
                if ($question->madhab_id && $m->madhab_id === $question->madhab_id) {
                    return 0 . '_' . $m->name;
                }
                return 1 . '_' . $m->name;
            })
            ->values();

        return view('admin.questions.show', compact('question', 'muftis', 'logs'));
    }

    /** Full fatwa history / timeline */
    public function history(QuestionAndAnswer $question)
    {
        $question->load([
            'user', 'category', 'assignedMufti',
            'publisher', 'statusHistory.actor'
        ]);

        $logs = FatwaActivityLog::getComprehensiveTimeline($question);

        return view('admin.questions.history', compact('question', 'logs'));
    }

    /** Show Accept Question Form Page */
    public function showAcceptForm(QuestionAndAnswer $question)
    {
        if ($question->status !== 'pending') {
            return redirect()->route('admin.questions.show', $question)->with('error', 'This question has already been accepted or processed.');
        }

        $mainCategories = Category::whereNull('parent_id')->get();
        $subCategories  = Category::whereNotNull('parent_id')->get();

        return view('admin.questions.accept', compact('question', 'mainCategories', 'subCategories'));
    }

    /** Accept and update question details */
    public function accept(Request $request, QuestionAndAnswer $question)
    {
        $request->validate([
            'title'       => ['required', 'string', 'max:200'],
            'language'    => ['required', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
        ]);

        $previousStatus = $question->status;

        $question->update([
            'status'      => 'accepted',
            'title'       => $request->title,
            'language'    => $request->language,
            'category_id' => $request->category_id,
        ]);

        FatwaActivityLog::log(
            $question->id, 'accepted', 'Question Accepted & Categorised',
            'bi-check-circle', 'success',
            'Categorised under: ' . optional($question->fresh()->category)->name, Auth::id()
        );

        QuestionStatusHistory::create([
            'question_and_answer_id' => $question->id,
            'previous_status'        => $previousStatus,
            'new_status'             => 'accepted',
            'user_id'                => Auth::id(),
            'remarks'                => 'Question accepted and categorized under: ' . optional($question->fresh()->category)->name,
        ]);

        return redirect()->route('admin.questions.show', $question)->with('success', 'Question accepted and categorized successfully.');
    }

    /** Assign question to a Mufti */
    public function assign(Request $request, QuestionAndAnswer $question)
    {
        $request->validate([
            'mufti_id' => ['required', 'exists:users,id'],
        ]);

        $mufti = User::findOrFail($request->mufti_id);
        $previousStatus = $question->status;

        $question->update([
            'assigned_mufti_id' => $mufti->id,
            'status'            => 'assigned',
        ]);

        FatwaActivityLog::log(
            $question->id, 'assigned', 'Assigned ' . $question->question_id . ' to Mufti ' . $mufti->name,
            'bi-person-check', 'primary',
            'Assigned ' . $question->question_id . ' to Mufti ' . $mufti->name, Auth::id()
        );

        QuestionStatusHistory::create([
            'question_and_answer_id' => $question->id,
            'previous_status'        => $previousStatus,
            'new_status'             => 'assigned',
            'new_assigned_mufti_id'  => $mufti->id,
            'user_id'                => Auth::id(),
            'remarks'                => 'Assigned ' . $question->question_id . ' to Mufti ' . $mufti->name,
        ]);

        try {
            Mail::to($mufti->email)->send(new QuestionAssignedMail($question));
        } catch (\Exception $e) {
            // Fail silently if mail server is not configured
        }

        return redirect()->route('admin.questions.show', $question)->with('success', 'Question assigned to ' . $mufti->name);
    }

    /** Admin writes or edits answer on behalf of Mufti */
    public function createAnswer(QuestionAndAnswer $question)
    {
        $question->load('user', 'references');
        
        $madhab = $question->madhab;
        $muftis = User::whereHas('role', fn($q) => $q->where('name', 'mufti'))
            ->where('is_active', true)
            ->with('madhab')
            ->get()
            ->sortBy(function ($m) use ($question) {
                if ($question->madhab_id && $m->madhab_id === $question->madhab_id) {
                    return 0 . '_' . $m->name;
                }
                return 1 . '_' . $m->name;
            })
            ->values();

        return view('admin.questions.answer', compact('question', 'muftis'));
    }

    public function storeAnswer(Request $request, QuestionAndAnswer $question)
    {
        $request->validate([
            'mufti_id'         => ['required', 'exists:users,id'],
            'full_answer'      => ['required', 'string'],
            'language'         => ['nullable', 'string', 'in:English,Arabic,Urdu,Malayalam,Hindi'],
            'arabic_text.*'    => ['nullable', 'string'],
            'book_name.*'      => ['nullable', 'string'],
            'ref_language.*'   => ['nullable', 'string'],
            'question_title'   => ['nullable', 'string', 'max:200'],
            'question_body'    => ['nullable', 'string'],
        ]);

        $plainAnswer = trim(strip_tags($request->full_answer ?? ''));
        if (mb_strlen($plainAnswer) < 30) {
            return back()->withErrors([
                'full_answer' => 'The answer must contain at least 30 characters before it can be saved/published (currently ' . mb_strlen($plainAnswer) . ' characters).',
            ])->withInput();
        }

        // Update the question language if provided
        if ($request->filled('language')) {
            $question->update(['language' => $request->language]);
        }

        // Update the question title and body if provided
        if ($request->has('question_title')) {
            $question->update(['title' => $request->question_title]);
        }
        if ($request->has('question_body')) {
            $question->update(['body' => $request->question_body]);
        }

        $isPublished = $question->status === 'published';
        $previousStatus = $question->status;

        if ($isPublished) {
            $question->update([
                'answer'            => $request->full_answer,
                'assigned_mufti_id' => $request->mufti_id,
            ]);
            $question->references()->delete();

            // Handle featured image update for published fatwa
            if ($request->filled('featured_image_cropped')) {
                $imagePath = $this->saveCroppedImage($request->featured_image_cropped);
                if ($imagePath) {
                    $question->update(['featured_image' => $imagePath]);
                }
            }
        } else {
            $question->update([
                'draft_answer'      => $request->full_answer,
                'status'            => 'answered',
                'assigned_mufti_id' => $request->mufti_id,
            ]);
            $question->draftReferences()->delete();
        }

        // Save supporting references
        if ($request->filled('arabic_text')) {
            foreach ($request->arabic_text as $i => $text) {
                if ($text) {
                    Reference::create([
                        'question_and_answer_id' => $question->id,
                        'arabic_text'            => $text,
                        'book_name'              => $request->book_name[$i] ?? '',
                        'language'               => $request->ref_language[$i] ?? 'Arabic',
                        'sort_order'             => $i,
                        'is_draft'               => !$isPublished,
                    ]);
                }
            }
        }

        if (!$isPublished) {
            FatwaActivityLog::log(
                $question->id, 'answer_submitted', 'Answer Written & Submitted for Review',
                'bi-pencil-square', 'info',
                'Written by: ' . (User::find($request->mufti_id)?->name ?? 'Admin'),
                Auth::id()
            );

            QuestionStatusHistory::create([
                'question_and_answer_id' => $question->id,
                'previous_status'        => $previousStatus,
                'new_status'             => 'answered',
                'user_id'                => Auth::id(),
                'remarks'                => 'Answer written & submitted by: ' . (User::find($request->mufti_id)?->name ?? 'Admin'),
            ]);

            return redirect()->route('admin.questions.verify', $question)->with('success', 'Answer saved. Now verify and publish.');
        }

        FatwaActivityLog::log(
            $question->id, 'answer_updated', 'Published Answer Updated',
            'bi-pencil', 'warning',
            'Answer edited after publishing.', Auth::id()
        );

        QuestionStatusHistory::create([
            'question_and_answer_id' => $question->id,
            'previous_status'        => $previousStatus,
            'new_status'             => 'published',
            'user_id'                => Auth::id(),
            'remarks'                => 'Published answer updated by Admin.',
        ]);

        return redirect()->route('admin.questions.show', $question)->with('success', 'Published answer updated successfully.');
    }

    /** Admin verifies Mufti's answer */
    public function verify(QuestionAndAnswer $question)
    {
        $question->load('references', 'user');

        $writtenByMuftiId = $question->assigned_mufti_id;
        $madhab = $question->madhab;
        $verifierMuftis = User::whereHas('role', fn($q) => $q->where('name', 'mufti'))
            ->where('is_active', true)
            ->where('id', '!=', $writtenByMuftiId)
            ->with('madhab')
            ->get()
            ->sortBy(function ($m) use ($question) {
                if ($question->madhab_id && $m->madhab_id === $question->madhab_id) {
                    return 0 . '_' . $m->name;
                }
                return 1 . '_' . $m->name;
            })
            ->values();

        $approverMuftis = User::whereHas('role', fn($q) => $q->where('name', 'mufti'))
            ->where('can_check_approve', true)
            ->get();

        return view('admin.fatwas.verify', compact('question', 'verifierMuftis', 'approverMuftis'));
    }

    public function approve(Request $request, QuestionAndAnswer $question)
    {
        $request->validate([
            'verified_by'          => ['required', 'array'],
            'verified_by.*'        => ['exists:users,id'],
            'checked_approved_by'  => ['nullable', 'exists:users,id'],
        ]);

        // Ensure Question Title is present before publishing
        if (!$question->title || strlen($question->title) > 200) {
            return redirect()->back()->withErrors(['title' => 'A Question Summary (max 200 chars) is mandatory for publishing. Please edit the question details first.']);
        }

        // Ensure Answer has at least 30 characters before publishing
        $answerText = trim(strip_tags($question->draft_answer ?? ($question->answer ?? '')));
        if (mb_strlen($answerText) < 30) {
            return redirect()->back()->withErrors(['answer' => 'Cannot publish: The fatwa answer must contain at least 30 characters (currently ' . mb_strlen($answerText) . ' characters).']);
        }

        $featuredImagePath = null;
        if ($request->filled('featured_image_cropped')) {
            $featuredImagePath = $this->saveCroppedImage($request->featured_image_cropped);
        } elseif ($request->hasFile('featured_image')) {
            $image = $request->file('featured_image');
            $filename = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/fatwas'), $filename);
            $featuredImagePath = 'uploads/fatwas/' . $filename;
        }

        $previousStatus = $question->status;

        $isPublic = $request->input('publish_visibility', 'public') !== 'private'; // Default is Public (is_public = true)

        $updateData = [
            'status'              => 'published',
            'is_public'           => $isPublic,
            'answer'              => $question->draft_answer ?? $question->answer,
            'draft_answer'        => null,
            'published_by'        => Auth::id(),
            'published_at'        => now(),
            'verified_by'         => $request->verified_by,
            'checked_approved_by' => $request->checked_approved_by ?: null,
        ];
        if ($featuredImagePath) {
            $updateData['featured_image'] = $featuredImagePath;
        }

        $question->update($updateData);

        // Convert draft references to live references
        $question->references()->delete(); // Clear existing live ones
        $question->draftReferences()->update(['is_draft' => false]); // Make draft references live

        $pubTypeLabel = $isPublic ? 'Fatwa Published Publicly' : 'Fatwa Published Privately (Questioner & Shared Link Only)';

        FatwaActivityLog::log(
            $question->id, 'published', $pubTypeLabel,
            'bi-patch-check-fill', 'success',
            $pubTypeLabel . ' by: ' . Auth::user()->name .
            (count($request->verified_by) > 0 ? ' | Verified by: ' . User::whereIn('id', $request->verified_by)->pluck('name')->implode(', ') : '') .
            ($request->checked_approved_by ? ' | Approved by: ' . optional(User::find($request->checked_approved_by))->name : ''),
            Auth::id()
        );

        QuestionStatusHistory::create([
            'question_and_answer_id' => $question->id,
            'previous_status'        => $previousStatus,
            'new_status'             => 'published',
            'user_id'                => Auth::id(),
            'remarks'                => $pubTypeLabel . '. Published by: ' . Auth::user()->name . 
                                       (count($request->verified_by) > 0 ? ' | Verified by: ' . User::whereIn('id', $request->verified_by)->pluck('name')->implode(', ') : '') . 
                                       ($request->checked_approved_by ? ' | Approved by: ' . optional(User::find($request->checked_approved_by))->name : ''),
        ]);

        // Notify questioner
        try {
            Mail::to($question->user->email)->send(new FatwaPublishedMail($question));
        } catch (\Exception $e) {
            // Fail silently if mail server is not configured
        }

        return redirect()->route('admin.questions.index')->with('success', $isPublic ? 'Fatwa published publicly to archive successfully!' : 'Fatwa published privately for questioner & shared-link recipients!');
    }

    /**
     * Toggle public archive visibility for a published fatwa
     */
    public function togglePublicVisibility(Request $request, QuestionAndAnswer $question)
    {
        $newVisibility = !$question->is_public;
        $question->update(['is_public' => $newVisibility]);

        $label = $newVisibility 
            ? 'Fatwa Made Publicly Visible in Archive' 
            : 'Fatwa Unpublished from Public (Now Private to Questioner & Shared Link)';
        
        FatwaActivityLog::log(
            $question->id, 'visibility_changed', $label,
            $newVisibility ? 'bi-globe' : 'bi-lock-fill',
            $newVisibility ? 'success' : 'warning',
            'Visibility updated by Admin ' . Auth::user()->name,
            Auth::id()
        );

        QuestionStatusHistory::create([
            'question_and_answer_id' => $question->id,
            'previous_status'        => $question->status,
            'new_status'             => $question->status,
            'user_id'                => Auth::id(),
            'remarks'                => $label . ' by ' . Auth::user()->name,
        ]);

        return redirect()->back()->with('success', $newVisibility 
            ? 'Fatwa is now published publicly in the global archive and search.' 
            : 'Fatwa unpublished from public archive. (Accessible only to questioner & logged-in shared link users).');
    }

    /**
     * Unpublish fatwa completely back to answered/under_review
     */
    public function unpublish(Request $request, QuestionAndAnswer $question)
    {
        $previousStatus = $question->status;
        $targetStatus = $request->input('target_status', 'answered');

        $question->update([
            'status'       => $targetStatus,
            'is_public'    => false,
            'published_at' => null,
            'draft_answer' => $question->answer,
        ]);

        FatwaActivityLog::log(
            $question->id, 'unpublished', 'Fatwa Unpublished from Public',
            'bi-eye-slash-fill', 'warning',
            'Fatwa status reverted from ' . $previousStatus . ' to ' . $targetStatus . ' by Admin ' . Auth::user()->name,
            Auth::id()
        );

        QuestionStatusHistory::create([
            'question_and_answer_id' => $question->id,
            'previous_status'        => $previousStatus,
            'new_status'             => $targetStatus,
            'user_id'                => Auth::id(),
            'remarks'                => 'Fatwa unpublished and moved back to ' . $targetStatus . ' by ' . Auth::user()->name,
        ]);

        return redirect()->back()->with('success', 'Fatwa unpublished and moved back to ' . ucfirst($targetStatus) . '.');
    }

    public function reject(Request $request, QuestionAndAnswer $question)
    {
        $request->validate(['admin_notes' => 'required|string']);
        $previousStatus = $question->status;

        $question->update(['status' => 'under_review']);

        FatwaActivityLog::log(
            $question->id, 'rejected', 'Answer Rejected — Returned to Mufti',
            'bi-x-circle', 'danger',
            'Admin notes: ' . $request->admin_notes, Auth::id()
        );

        QuestionStatusHistory::create([
            'question_and_answer_id' => $question->id,
            'previous_status'        => $previousStatus,
            'new_status'             => 'under_review',
            'user_id'                => Auth::id(),
            'remarks'                => 'Answer rejected. Admin notes: ' . $request->admin_notes,
        ]);

        return redirect()->back()->with('success', 'Answer rejected with notes.');
    }

    public function requestCorrection(Request $request, QuestionAndAnswer $question)
    {
        $request->validate(['admin_notes' => 'required|string']);
        $previousStatus = $question->status;

        $question->update(['status' => 'under_review']);

        FatwaActivityLog::log(
            $question->id, 'correction_requested', 'Correction Requested from Mufti',
            'bi-arrow-repeat', 'warning',
            'Admin notes: ' . $request->admin_notes, Auth::id()
        );

        QuestionStatusHistory::create([
            'question_and_answer_id' => $question->id,
            'previous_status'        => $previousStatus,
            'new_status'             => 'under_review',
            'user_id'                => Auth::id(),
            'remarks'                => 'Correction requested from Mufti. Admin notes: ' . $request->admin_notes,
        ]);

        return redirect()->back()->with('success', 'Correction requested from Mufti.');
    }

    /** Add private scholarly comment to history */
    public function addHistoryComment(Request $request, QuestionAndAnswer $question)
    {
        $request->validate(['comment' => 'required|string|max:1000']);
        $isAdmin = Auth::user()->role?->name === 'admin';

        FatwaActivityLog::log(
            $question->id,
            'scholarly_comment',
            $isAdmin ? 'Admin Note / Feedback' : 'Mufti Message / Comment',
            'bi-chat-dots-fill',
            $isAdmin ? 'danger' : 'warning',
            $request->comment,
            Auth::id()
        );

        QuestionStatusHistory::create([
            'question_and_answer_id' => $question->id,
            'previous_status'        => $question->status,
            'new_status'             => $question->status,
            'user_id'                => Auth::id(),
            'remarks'                => ($isAdmin ? 'Admin Note: ' : 'Mufti Note: ') . $request->comment,
        ]);

        return redirect()->back()->with('success', 'Comment added to history.');
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

                $filename = 'fatwa_' . time() . '_' . Str::random(5) . '.' . $type;
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
