<?php

namespace App\Http\Controllers\Mufti;

use App\Http\Controllers\Controller;
use App\Mail\AnswerSubmittedMail;
use App\Models\MuftiAnswer;
use App\Models\Question;
use App\Models\SupportingReference;
use App\Models\FatwaActivityLog;
use App\Models\User;
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
        $assigned = Question::where('assigned_mufti_id', $user->id)
                            ->where('status', 'assigned')
                            ->count();
        $answered = MuftiAnswer::where('mufti_id', $user->id)->count();
        $recent   = Question::where('assigned_mufti_id', $user->id)->latest()->take(5)->get();

        // Performance Score Calculation
        $totalEver = Question::where('assigned_mufti_id', $user->id)->count();
        $score = 0;
        if ($totalEver > 0) {
            // 1. Base Completion Rate (0-70 pts)
            $completionRate = ($answered / $totalEver) * 70;

            // 2. Speed Factor (0-20 pts)
            // Average days to answer
            $avgDays = MuftiAnswer::where('mufti_id', $user->id)
                ->join('questions', 'mufti_answers.question_id', '=', 'questions.id')
                ->whereNotNull('questions.assigned_at')
                ->selectRaw('AVG(DATEDIFF(mufti_answers.submitted_at, questions.assigned_at)) as avg_days')
                ->value('avg_days') ?: 5; // Default to 5 days if no data
            
            $speedPoints = 0;
            if ($avgDays <= 2) $speedPoints = 20;
            elseif ($avgDays <= 5) $speedPoints = 15;
            elseif ($avgDays <= 10) $speedPoints = 10;
            else $speedPoints = 5;

            // 3. Volume Bonus (0-10 pts)
            // Rewards starting from 5 fatwas, max at 50
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

        // Tab counts for the badge pills
        $counts = [
            'assigned' => Question::where('assigned_mufti_id', $user->id)->where('status', 'assigned')->count(),
            'verified' => Question::whereHas('fatwa', fn($f) => $f->where('verified_by', $user->id))->count(),
            'approved' => Question::whereHas('fatwa', fn($f) => $f->where('checked_approved_by', $user->id))->count(),
        ];

        $query = Question::with('user', 'fatwa', 'category')->latest();

        if ($tab == 'verified') {
            $query->whereHas('fatwa', fn($f) => $f->where('verified_by', $user->id));
        } elseif ($tab == 'approved') {
            $query->whereHas('fatwa', fn($f) => $f->where('checked_approved_by', $user->id));
        } else {
            $query->where('assigned_mufti_id', $user->id);
        }

        $questions = $query->paginate(20)->withQueryString();

        return view('mufti.questions.index', compact('questions', 'tab', 'counts'));
    }

    public function pendingQuestions()
    {
        $user = Auth::user();
        $questions = Question::with('user', 'category')
            ->where('assigned_mufti_id', $user->id)
            ->where('status', 'assigned')
            ->latest()
            ->paginate(20);

        return view('mufti.questions.pending', compact('questions'));
    }

    public function showQuestion(Question $question)
    {
        abort_unless($question->assigned_mufti_id === Auth::id(), 403);
        $question->load([
            'user', 'files', 'muftiAnswer.supportingReferences',
            'activityLogs' => fn($q) => $q->orderBy('event_at', 'desc')
        ]);
        return view('mufti.questions.answer', compact('question'));
    }

    public function submitAnswer(Request $request, Question $question)
    {
        abort_unless($question->assigned_mufti_id === Auth::id(), 403);

        $request->validate([
            'summary'        => ['required', 'string', 'min:10'],
            'full_answer'    => ['required', 'string', 'min:30'],
            'arabic_text.*'  => ['nullable', 'string'],
            'book_name.*'    => ['nullable', 'string'],
            'ref_language.*' => ['nullable', 'string'],
        ]);

        // Update or create
        $answer = MuftiAnswer::updateOrCreate(
            ['question_id' => $question->id, 'mufti_id' => Auth::id()],
            [
                'summary'      => $request->summary,
                'full_answer'  => $request->full_answer,
                'status'       => 'submitted',
                'submitted_at' => now(),
            ]
        );

        // Clear old references and re-save
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

        $question->update(['status' => 'answered']);

        // Log the activity
        FatwaActivityLog::log(
            $question->id, 'answer_submitted', 'Answer Submitted for Review',
            'bi-pencil-square', 'info',
            'Draft completed and submitted to administration by scholar.',
            Auth::id()
        );

        // Notify admin
        $admin = User::role('admin')->first();
        if ($admin) {
            Mail::to($admin->email)->send(new AnswerSubmittedMail($answer));
        }

        return redirect()->route('mufti.questions')->with('success', 'Answer submitted for admin review.');
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
