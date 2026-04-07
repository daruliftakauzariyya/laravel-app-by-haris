<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AnswerSubmittedMail;
use App\Mail\FatwaPublishedMail;
use App\Mail\QuestionAssignedMail;
use App\Models\Category;
use App\Models\Fatwa;
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

    /** Accept and update question details */
    public function accept(Request $request, Question $question)
    {
        $request->validate([
            'language'    => ['required', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
        ]);

        $question->update([
            'status'      => 'accepted',
            'language'    => $request->language,
            'category_id' => $request->category_id,
            'accepted_at' => now(),
        ]);

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
            'arabic_text.*'    => ['nullable', 'string'],
            'book_name.*'      => ['nullable', 'string'],
            'ref_language.*'   => ['nullable', 'string'],
        ]);

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

        // If it was already published, we keep it published but update the answer
        if ($question->status === 'published') {
            $answer->update(['status' => 'reviewed']);
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
            return redirect()->route('admin.questions.verify', $question)->with('success', 'Answer saved. Now verify and publish.');
        }

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

        $answer = $question->muftiAnswer;
        $answer->update([
            'status'      => 'approved',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        $fatwa = Fatwa::create([
            'question_id'         => $question->id,
            'mufti_answer_id'     => $answer->id,
            'published_by'        => Auth::id(),
            'verified_by'         => $request->verified_by,
            'checked_approved_by' => $request->checked_approved_by,
            'published_at'        => now(),
        ]);

        $question->update(['status' => 'published']);

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
        return redirect()->back()->with('success', 'Correction requested from Mufti.');
    }

    /** Download question file */
    public function downloadFile(\App\Models\QuestionFile $file)
    {
        return Storage::disk('local')->download($file->file_path, $file->original_name);
    }
}
