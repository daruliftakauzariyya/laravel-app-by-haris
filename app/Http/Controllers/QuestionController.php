<?php

namespace App\Http\Controllers;

use App\Mail\NewQuestionMail;
use App\Models\Category;
use App\Models\FatwaActivityLog;
use App\Models\QuestionAndAnswer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class QuestionController extends Controller
{
    public function create()
    {
        $madhabs = \App\Models\Madhab::all();
        return view('ask-question', compact('madhabs'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // Check for 5-minute patience (Throttling)
        $lastQuestion = QuestionAndAnswer::where('user_id', Auth::id())
            ->latest()
            ->first();

        if ($lastQuestion && $lastQuestion->created_at->addMinutes(5)->isFuture()) {
            $diff = $lastQuestion->created_at->addMinutes(5)->diffForHumans();
            return redirect()->back()->withInput()->with('error', "Please wait. You can submit another question in {$diff}. A 5-minute patience is required between submissions.");
        }

        $rules = [
            'body'        => ['required', 'string', 'min:20'],
            'madhab_id'   => ['required', 'exists:madhabs,id'],
            'title'       => ['required', 'string', 'max:200'],
            'category_id' => ['nullable', 'exists:categories,id'],
        ];

        $request->validate($rules);

        // Check if the user's profile is incomplete
        $hasMobile  = !empty($user->mobile);
        $hasGender  = !empty($user->gender) && strtolower($user->gender) !== 'not set';
        $hasMadhab  = !empty($user->madhab_id);
        $hasAddress = !empty($user->address_line_1);

        $isProfileIncomplete = !$hasMobile || !$hasGender || !$hasMadhab || !$hasAddress || !$user->profile_completed;

        if ($isProfileIncomplete) {
            // Save question to session
            session([
                'pending_question' => [
                    'title'          => $request->title,
                    'body'           => $request->body,
                    'madhab_id'      => $request->madhab_id,
                    'category_id'    => $request->category_id,
                ]
            ]);

            return redirect()->route('profile.complete');
        }

        // Profile is complete, post the question directly
        $question = self::saveQuestion(Auth::id(), [
            'title'          => $request->title,
            'body'           => $request->body,
            'madhab_id'      => $request->madhab_id,
            'category_id'    => $request->category_id,
        ]);

        return redirect()->route('home')->with('success', 'Your question has been submitted successfully! Question ID: ' . $question->question_id);
    }

    /**
     * Common helper method to save the question, handle file migration, log activity, and notify admin/user.
     */
    public static function saveQuestion($userId, array $data)
    {
        $question = QuestionAndAnswer::create([
            'question_id' => QuestionAndAnswer::generateQuestionId(),
            'user_id'     => $userId,
            'category_id' => $data['category_id'] ?? null,
            'title'       => $data['title'],
            'body'        => $data['body'],
            'madhab_id'   => $data['madhab_id'],
            'status'      => 'pending',
        ]);

        $madhabName = \App\Models\Madhab::find($data['madhab_id'])?->name ?? 'General';

        // Log the first event in the fatwa timeline
        FatwaActivityLog::log(
            $question->id, 'question_submitted', 'Question Submitted by Questioner',
            'bi-send', 'secondary',
            'Submitted in ' . $madhabName . ' madhab.', $userId,
            null, $question->created_at
        );

        // Email admin
        $admin = User::whereHas('role', fn($q) => $q->where('name', 'admin'))->first();
        if ($admin) {
            try {
                Mail::to($admin->email)->send(new \App\Mail\NewQuestionMail($question));
            } catch (\Exception $e) {
                \Log::error("Failed to send NewQuestionMail to admin: " . $e->getMessage());
            }
        }

        // Email the user
        $user = User::find($userId);
        if ($user) {
            try {
                Mail::to($user->email)->send(new \App\Mail\UserQuestionSubmittedMail($question));
            } catch (\Exception $e) {
                \Log::error("Failed to send UserQuestionSubmittedMail to user: " . $e->getMessage());
            }
        }

        return $question;
    }
}
