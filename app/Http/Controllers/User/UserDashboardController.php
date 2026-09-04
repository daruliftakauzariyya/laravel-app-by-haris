<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\QuestionAndAnswer;
use App\Models\Madhab;

class UserDashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $questions = QuestionAndAnswer::where('user_id', $userId)
                        ->orderBy('created_at', 'desc')
                        ->paginate(9);

        $stats = [
            'total'    => QuestionAndAnswer::where('user_id', $userId)->count(),
            'answered' => QuestionAndAnswer::where('user_id', $userId)->whereIn('status', ['answered', 'published'])->count(),
            'pending'  => QuestionAndAnswer::where('user_id', $userId)->where('status', 'pending')->count(),
        ];
                        
        $madhabs = Madhab::all();
        return view('user.dashboard', compact('questions', 'stats', 'madhabs'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'mobile'          => 'nullable|string|max:20',
            'gender'          => 'nullable|string|in:Male,Female',
            'madhab_id'       => 'nullable|exists:madhabs,id',
            'address_line_1'  => 'nullable|string|max:255',
            'address_line_2'  => 'nullable|string|max:255',
            'country'         => 'nullable|string|max:100',
            'state'           => 'nullable|string|max:100',
            'district'        => 'nullable|string|max:100',
            'mother_language' => 'nullable|string|max:50',
            'avatar_file'     => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle Avatar Upload
        if ($request->hasFile('avatar_file')) {
            // Delete old avatar if exists and it's a local file
            if ($user->avatar && str_contains($user->avatar, '/assets/img/avatars/')) {
                $oldPath = public_path(\Illuminate\Support\Str::after($user->avatar, asset('')));
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $file = $request->file('avatar_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $targetDir = public_path('assets/img/avatars');
            
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            $file->move($targetDir, $filename);
            $validated['avatar'] = asset('assets/img/avatars/' . $filename);
        }

        $user->update($validated);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        Auth::user()->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
        ]);

        return back()->with('success', 'Password updated successfully. You can now use your new password.');
    }

    public function show(QuestionAndAnswer $question)
    {
        // Security check: Make sure this question belongs to the auth user
        if ($question->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this query.');
        }

        // Load valid relationships for the details page
        $question->load([
            'category',
            'madhab',
            'assignedMufti',
            'publisher',
            'references',
            'draftReferences',
        ]);

        // Fetch related questions from the same category
        $relatedQuestions = collect();
        if ($question->category_id) {
            $relatedQuestions = QuestionAndAnswer::where('category_id', $question->category_id)
                ->where('id', '!=', $question->id)
                ->where('status', 'published')
                ->latest()
                ->take(5)
                ->get();
        }

        return view('user.question', compact('question', 'relatedQuestions'));
    }
}
