<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $questions = \App\Models\Question::where('user_id', $userId)
                        ->orderBy('created_at', 'desc')
                        ->paginate(9);

        $stats = [
            'total'    => \App\Models\Question::where('user_id', $userId)->count(),
            'answered' => \App\Models\Question::where('user_id', $userId)->whereIn('status', ['answered', 'published'])->count(),
            'pending'  => \App\Models\Question::where('user_id', $userId)->where('status', 'pending')->count(),
        ];
                        
        return view('user.dashboard', compact('questions', 'stats'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'mobile'          => 'nullable|string|max:20',
            'country'         => 'nullable|string|max:100',
            'madhab'          => 'nullable|string|max:50',
            'mother_language' => 'nullable|string|max:50',
        ]);

        $user->update($validated);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        Auth::user()->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
        ]);

        return back()->with('success', 'Password updated successfully.');
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
