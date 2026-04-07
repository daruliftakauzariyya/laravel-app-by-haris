<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Question;
use App\Models\Fatwa;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class QuestionerController extends Controller
{
    /**
     * List all users who have ever asked a question.
     */
    public function index(Request $request)
    {
        $query = User::whereHas('questions')
            ->withCount([
                'questions as total_questions',
                'questions as pending_questions' => fn($q) => $q->whereIn('status', ['pending', 'accepted', 'assigned', 'answered']),
                'questions as published_questions' => fn($q) => $q->where('status', 'published'),
            ])
            ->with('questions') // for latest activity
            ->latest();

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(fn($q) => $q->where('name', 'like', "%$term%")->orWhere('email', 'like', "%$term%"));
        }

        $questioners = $query->paginate(20)->withQueryString();

        return view('admin.questioners.index', compact('questioners'));
    }

    /**
     * Detail page for a single questioner.
     */
    public function show(User $user)
    {
        // Load all questions with related fatwa, and the user-view record
        $questions = Question::where('user_id', $user->id)
            ->with([
                'fatwa',
                'fatwa.userViews' => fn($q) => $q->where('user_id', $user->id),
            ])
            ->latest()
            ->get()
            ->map(function ($question) use ($user) {
                $fatwa = $question->fatwa;

                // Days from question submission to publishing
                $daysToPublish = null;
                if ($fatwa && $fatwa->published_at) {
                    $daysToPublish = (int) $question->created_at->diffInDays($fatwa->published_at);
                }

                // How many times the questioner personally viewed their fatwa
                $userViewCount = $fatwa
                    ? ($fatwa->userViews->where('user_id', $user->id)->first()?->views ?? 0)
                    : 0;

                return [
                    'question'       => $question,
                    'fatwa'          => $fatwa,
                    'days_to_publish' => $daysToPublish,
                    'user_views'     => $userViewCount,
                    'public_reads'   => $fatwa?->views ?? 0,
                ];
            });

        // Group stats
        $stats = [
            'total'     => $questions->count(),
            'pending'   => $questions->filter(fn($r) => in_array($r['question']->status, ['pending','accepted','assigned','answered']))->count(),
            'published' => $questions->filter(fn($r) => $r['question']->status === 'published')->count(),
            'total_reads' => $questions->sum('public_reads'),
        ];

        return view('admin.questioners.show', compact('user', 'questions', 'stats'));
    }
}
