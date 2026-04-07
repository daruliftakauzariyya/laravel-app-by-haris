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

        // We load the relationship if it exists so we can display answers
        $question->load(['category', 'muftiAnswer']);

        return view('user.question', compact('question'));
    }
}
