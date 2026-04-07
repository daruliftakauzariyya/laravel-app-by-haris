<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    public function index()
    {
        $questions = \App\Models\Question::where('user_id', Auth::id())
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);
                        
        return view('user.dashboard', compact('questions'));
    }

    public function show(\App\Models\Question $question)
    {
        // Security check: Make sure this question belongs to the auth user
        if ($question->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this query.');
        }

        // Load all relationships needed for the full details page
        $question->load([
            'category',
            'muftiAnswer.mufti',
            'muftiAnswer.reviewer',
            'muftiAnswer.supportingReferences',
            'fatwa.publisher',
            'fatwa.verifiedBy',
            'fatwa.checkedApprovedBy',
        ]);

        // Fetch related questions from the same category
        $relatedQuestions = collect();
        if ($question->category_id) {
            $relatedQuestions = \App\Models\Question::where('category_id', $question->category_id)
                ->where('id', '!=', $question->id)
                ->where('status', 'published')
                ->latest()
                ->take(5)
                ->get();
        }

        return view('user.question', compact('question', 'relatedQuestions'));
    }
}
