<?php

namespace App\Http\Controllers\Mufti;

use App\Http\Controllers\Controller;
use App\Mail\AnswerSubmittedMail;
use App\Models\QuestionAndAnswer;
use App\Models\Reference;
use App\Models\FatwaActivityLog;
use App\Models\User;
use App\Models\QuestionStatusHistory;
use App\Traits\HandleAvatars;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class MuftiController extends Controller
{
    use HandleAvatars;

    public function dashboard()
    {
        $user = Auth::user();
        $assigned = QuestionAndAnswer::where('assigned_mufti_id', $user->id)
                            ->where('status', 'assigned')
                            ->count();
        $answered = QuestionAndAnswer::where('assigned_mufti_id', $user->id)
                            ->whereNotNull('answer')
                            ->count();
        $recent   = QuestionAndAnswer::with('madhab')->where('assigned_mufti_id', $user->id)->latest()->take(5)->get();

        // Performance Score Calculation
        $totalEver = QuestionAndAnswer::where('assigned_mufti_id', $user->id)->count();
        $score = 0;
        if ($totalEver > 0) {
            // 1. Base Completion Rate (0-70 pts)
            $completionRate = ($answered / $totalEver) * 70;

            // 2. Speed Factor (0-20 pts)
            $avgDays = QuestionAndAnswer::where('assigned_mufti_id', $user->id)
                ->where('status', '!=', 'assigned')
                ->selectRaw('AVG(DATEDIFF(updated_at, created_at)) as avg_days')
                ->value('avg_days') ?: 5;
            
            $speedPoints = 0;
            if ($avgDays <= 2) $speedPoints = 20;
            elseif ($avgDays <= 5) $speedPoints = 15;
            elseif ($avgDays <= 10) $speedPoints = 10;
            else $speedPoints = 5;

            // 3. Volume Bonus (0-10 pts)
            $volumePoints = min(10, ($answered / 5));

            $score = round($completionRate + $speedPoints + $volumePoints);
            $score = min(100, $score);
        }

        return view('mufti.dashboard', compact('assigned', 'answered', 'recent', 'score'));
    }

    public function questions(Request $request)
    {
        $user = Auth::user();
        $tab = $request->get('tab', 'assigned');

        // Tab counts for the badge pills using statusHistory for verified actions
        $counts = [
            'assigned' => QuestionAndAnswer::where('assigned_mufti_id', $user->id)->where('status', 'assigned')->count(),
            'verified' => QuestionAndAnswer::whereHas('statusHistory', fn($h) => $h->where('user_id', $user->id)->where('new_status', 'approved'))->count(),
            'approved' => QuestionAndAnswer::where('published_by', $user->id)->count(),
        ];

        $query = QuestionAndAnswer::with('user', 'category', 'madhab')->latest();

        if ($tab == 'verified') {
            $query->whereHas('statusHistory', fn($h) => $h->where('user_id', $user->id)->where('new_status', 'approved'));
        } elseif ($tab == 'approved') {
            $query->where('published_by', $user->id);
        } else {
            $query->where('assigned_mufti_id', $user->id);
        }

        $questions = $query->paginate(20)->withQueryString();

        return view('mufti.questions.index', compact('questions', 'tab', 'counts'));
    }

    public function pendingQuestions()
    {
        $user = Auth::user();
        $questions = QuestionAndAnswer::with('user', 'category')
            ->where('assigned_mufti_id', $user->id)
            ->where('status', 'assigned')
            ->latest()
            ->paginate(20);

        return view('mufti.questions.pending', compact('questions'));
    }

    public function showQuestion(QuestionAndAnswer $question)
    {
        abort_unless($question->assigned_mufti_id === Auth::id(), 403);

        FatwaActivityLog::log(
            $question->id,
            'mufti_opened',
            'Mufti Opened the Fatwa',
            'bi-eye-fill',
            'info',
            'Mufti opened the fatwa @ ' . now()->format('Y-m-d H:i:s'),
            Auth::id()
        );

        $question->load([
            'user', 'madhab', 'references',
            'statusHistory' => fn($q) => $q->orderBy('created_at', 'desc')
        ]);
        return view('mufti.questions.answer', compact('question'));
    }

    public function submitAnswer(Request $request, QuestionAndAnswer $question)
    {
        abort_unless($question->assigned_mufti_id === Auth::id(), 403);

        $request->validate([
            'full_answer'    => ['required', 'string', 'min:30'],
            'arabic_text.*'  => ['nullable', 'string'],
            'book_name.*'    => ['nullable', 'string'],
            'ref_language.*' => ['nullable', 'string'],
            'question_title' => ['nullable', 'string', 'max:200'],
            'question_body'  => ['nullable', 'string'],
        ]);

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
            // Update live answer directly for typos/immediate edits
            $question->update([
                'answer' => $request->full_answer,
            ]);
            $question->references()->delete();
        } else {
            // Store as draft answer for pending review
            $question->update([
                'draft_answer' => $request->full_answer,
                'status'       => 'answered',
            ]);
            $question->draftReferences()->delete();
        }

        // Save references
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

        // Log the activity
        FatwaActivityLog::log(
            $question->id, 'answer_submitted', 'Answer Submitted for Review',
            'bi-pencil-square', 'info',
            'Draft completed and submitted to administration by scholar.',
            Auth::id()
        );

        QuestionStatusHistory::create([
            'question_and_answer_id' => $question->id,
            'previous_status'        => $previousStatus,
            'new_status'             => $isPublished ? 'published' : 'answered',
            'user_id'                => Auth::id(),
            'remarks'                => 'Answer submitted by scholar.',
        ]);

        // Notify admin
        $admin = User::whereHas('role', fn($q) => $q->where('name', 'admin'))->first();
        if ($admin) {
            try {
                Mail::to($admin->email)->send(new AnswerSubmittedMail($question));
            } catch (\Exception $e) {
                // Fail silently if mail server is not configured
            }
        }

        return redirect()->route('mufti.questions')->with('success', 'Answer submitted successfully.');
    }

    public function saveDraft(Request $request, QuestionAndAnswer $question)
    {
        abort_unless($question->assigned_mufti_id === Auth::id(), 403);

        $request->validate([
            'full_answer'    => ['nullable', 'string'],
            'arabic_text.*'  => ['nullable', 'string'],
            'book_name.*'    => ['nullable', 'string'],
            'ref_language.*' => ['nullable', 'string'],
            'question_title' => ['nullable', 'string', 'max:200'],
            'question_body'  => ['nullable', 'string'],
        ]);

        // Update the question title and body if provided
        if ($request->has('question_title')) {
            $question->update(['title' => $request->question_title]);
        }
        if ($request->has('question_body')) {
            $question->update(['body' => $request->question_body]);
        }

        // Save draft answer
        $question->update([
            'draft_answer' => $request->full_answer,
        ]);

        // Save references
        $question->draftReferences()->delete();
        if ($request->filled('arabic_text')) {
            foreach ($request->arabic_text as $i => $text) {
                if ($text) {
                    Reference::create([
                        'question_and_answer_id' => $question->id,
                        'arabic_text'            => $text,
                        'book_name'              => $request->book_name[$i] ?? '',
                        'language'               => $request->ref_language[$i] ?? 'Arabic',
                        'sort_order'             => $i,
                        'is_draft'               => true,
                    ]);
                }
            }
        }

        // Log the activity
        FatwaActivityLog::log(
            $question->id, 'draft_saved', 'Draft Saved by Scholar',
            'bi-journal-text', 'secondary',
            'Mufti saved a draft of fatwa ' . $question->question_id . ' @ ' . now()->format('Y-m-d H:i:s'),
            Auth::id()
        );

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Draft saved successfully.',
                'time' => now()->format('H:i:s'),
            ]);
        }

        return redirect()->back()->with('success', 'Draft saved successfully.');
    }

    public function profile()
    {
        return view('mufti.profile', ['user' => Auth::user()]);
    }

    public function updateProfile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'email'       => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'mobile'      => ['nullable', 'string', 'max:20'],
            'password'    => ['nullable', 'string', 'min:8', 'confirmed'],
            'avatar_data' => ['nullable', 'string'], // Base64 from Cropper
        ]);

        $user->email = $request->email;
        $user->mobile = $request->mobile;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $msg = 'Profile updated successfully.';

        if ($request->filled('avatar_data')) {
            // Store as PENDING for admin review
            $path = $this->processAndStoreAvatar($request->avatar_data, $user->id, true);
            if ($path) {
                $msg = 'Profile updated. Your new photo has been submitted for admin review.';
            }
        }

        $user->save();

        return redirect()->route('mufti.profile')->with('success', $msg);
    }
}
