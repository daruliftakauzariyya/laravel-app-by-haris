<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MuftiAnswer;
use App\Models\Question;
use App\Models\SupportingReference;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class MuftiApiController extends Controller
{
    /**
     * Scholar Dashboard Stats (Unified JSON)
     */
    public function dashboard()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        $assignedCount = Question::where('assigned_mufti_id', $user->id)
                            ->where('status', 'assigned')
                            ->count();
                            
        $answeredCount = MuftiAnswer::where('mufti_id', $user->id)->count();
        
        $recentTasks = Question::with('category', 'madhab')
                            ->where('assigned_mufti_id', $user->id)
                            ->latest()
                            ->take(5)
                            ->get();

        // High-fidelity performance score logic
        $totalEver = Question::where('assigned_mufti_id', $user->id)->count();
        $score = 0;
        if ($totalEver > 0) {
            $completionRate = ($answeredCount / $totalEver) * 70;
            $speedPoints = 15; // Placeholder for now
            $volumePoints = min(10, ($answeredCount / 5));
            $score = round($completionRate + $speedPoints + $volumePoints);
            $score = min(100, $score);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => [
                    'name' => $user->name,
                    'avatar' => $user->avatar ? asset('storage/' . $user->avatar) : null,
                ],
                'stats' => [
                    'assigned' => $assignedCount,
                    'answered' => $answeredCount,
                    'score'    => $score,
                ],
                'recent_tasks' => $recentTasks
            ]
        ]);
    }

    /**
     * Filterable Task List
     */
    public function questions(Request $request)
    {
        $user = Auth::user();
        $status = $request->get('status', 'assigned'); // assigned, answered, etc.

        $query = Question::with('user', 'category', 'madhab')
                    ->where('assigned_mufti_id', $user->id);

        if ($status === 'assigned') {
            $query->where('status', 'assigned');
        } elseif ($status === 'answered') {
            $query->where('status', 'answered');
        }

        $questions = $query->latest()->paginate(15);

        return response()->json([
            'status' => 'success',
            'data' => $questions
        ]);
    }

    /**
     * Task Details
     */
    public function showQuestion(Question $question)
    {
        if ($question->assigned_mufti_id !== Auth::id()) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $question->load(['user', 'files', 'madhab', 'muftiAnswer.supportingReferences']);

        return response()->json([
            'status' => 'success',
            'data' => $question
        ]);
    }

    /**
     * Submit Fatwa Answer
     */
    public function submitAnswer(Request $request, Question $question)
    {
        if ($question->assigned_mufti_id !== Auth::id()) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'summary'     => 'required|string|min:10',
            'full_answer' => 'required|string|min:30',
            'references'  => 'nullable|array',
        ]);

        $answer = MuftiAnswer::updateOrCreate(
            ['question_id' => $question->id, 'mufti_id' => Auth::id()],
            [
                'summary'      => $request->summary,
                'full_answer'  => $request->full_answer,
                'status'       => 'submitted',
                'submitted_at' => now(),
            ]
        );

        // References sync
        if ($request->has('references')) {
            $answer->supportingReferences()->delete();
            foreach ($request->references as $index => $ref) {
                if (!empty($ref['arabic_text'])) {
                    SupportingReference::create([
                        'mufti_answer_id' => $answer->id,
                        'arabic_text'     => $ref['arabic_text'],
                        'book_name'       => $ref['book_name'] ?? '',
                        'language'        => $ref['language'] ?? 'Arabic',
                        'sort_order'      => $index,
                    ]);
                }
            }
        }

        $question->update(['status' => 'answered']);

        return response()->json([
            'status' => 'success',
            'message' => 'Answer submitted successfully'
        ]);
    }

    /**
     * Profile Stats
     */
    public function profile()
    {
        $user = Auth::user();
        return response()->json([
            'status' => 'success',
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
                'mobile' => $user->mobile,
                'avatar' => $user->avatar ? asset('storage/' . $user->avatar) : null,
            ]
        ]);
    }
}
