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
        return view('mufti.dashboard', compact('assigned', 'answered', 'recent'));
    }

    public function questions()
    {
        $questions = Question::where('assigned_mufti_id', Auth::id())
                             ->with('user')
                             ->latest()
                             ->paginate(20);
        return view('mufti.questions.index', compact('questions'));
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
            'name'            => ['required', 'string', 'max:255'],
            'mobile'          => ['nullable', 'string', 'max:20'],
            'country'         => ['nullable', 'string', 'max:100'],
            'mother_language' => ['nullable', 'string', 'max:50'],
        ]);

        Auth::user()->update($request->only('name', 'mobile', 'country', 'mother_language'));
        return redirect()->route('mufti.profile')->with('success', 'Profile updated.');
    }
}
