<?php

namespace App\Http\Controllers\Mufti;

use App\Http\Controllers\Controller;
use App\Mail\AnswerSubmittedMail;
use App\Models\MuftiAnswer;
use App\Models\Question;
use App\Models\SupportingReference;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class MuftiController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $assigned = Question::where('assigned_mufti_id', $user->id)
                            ->whereIn('status', ['assigned', 'under_review'])
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
            'assigned' => Question::where('assigned_mufti_id', $user->id)->count(),
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

    public function showQuestion(Question $question)
    {
        abort_unless($question->assigned_mufti_id === Auth::id(), 403);
        $question->load('user', 'files', 'muftiAnswer.supportingReferences');
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
        $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'mobile'            => ['nullable', 'string', 'max:20'],
            'country'           => ['nullable', 'string', 'max:100'],
            'mother_language'   => ['nullable', 'string', 'max:50'],
            'can_check_approve' => ['nullable', 'boolean'],
        ]);

        $data = $request->only('name', 'mobile', 'country', 'mother_language');
        $data['can_check_approve'] = $request->boolean('can_check_approve');

        Auth::user()->update($data);
        return redirect()->route('mufti.profile')->with('success', 'Profile updated.');
    }
}
