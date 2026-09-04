<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\QuestionAndAnswer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MuftiApiController extends Controller
{
    /**
     * Scholar Dashboard Stats (Unified JSON)
     */
    public function dashboard()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        $assignedCount = QuestionAndAnswer::where('assigned_mufti_id', $user->id)
                            ->where('status', 'assigned')
                            ->count();
                            
        $answeredCount = QuestionAndAnswer::where('assigned_mufti_id', $user->id)
                            ->whereIn('status', ['answered', 'approved', 'published'])
                            ->count();
        
        $recentTasks = QuestionAndAnswer::with('category', 'madhab')
                            ->where('assigned_mufti_id', $user->id)
                            ->latest()
                            ->take(5)
                            ->get();

        // High-fidelity performance score logic
        $totalEver = QuestionAndAnswer::where('assigned_mufti_id', $user->id)->count();
        $score = 0;
        if ($totalEver > 0) {
            $completionRate = ($answeredCount / $totalEver) * 70;
            $speedPoints = 15;
            $volumePoints = min(10, ($answeredCount / 5));
            $score = round($completionRate + $speedPoints + $volumePoints);
            $score = min(100, $score);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => [
                    'name' => $user->name,
                    'avatar' => $user->avatar ? asset(ltrim($user->avatar, '/')) : null,
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

        $query = QuestionAndAnswer::with('user', 'category', 'madhab')
                    ->where('assigned_mufti_id', $user->id);

        if ($status === 'assigned') {
            $query->where('status', 'assigned');
        } elseif ($status === 'answered') {
            $query->whereIn('status', ['answered', 'approved', 'published']);
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
    public function showQuestion($id)
    {
        $question = QuestionAndAnswer::findOrFail($id);

        if ($question->assigned_mufti_id !== Auth::id()) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $question->load(['user', 'madhab', 'references', 'draftReferences']);

        \App\Models\FatwaActivityLog::log(
            $question->id,
            'mufti_opened',
            'Mufti ' . Auth::user()->name . ' Opened the Fatwa',
            'bi-eye-fill',
            'info',
            'Mufti ' . Auth::user()->name . ' opened the fatwa @ ' . now()->format('Y-m-d H:i:s'),
            Auth::id()
        );

        return response()->json([
            'status' => 'success',
            'data' => $question
        ]);
    }

    /**
     * Submit Fatwa Answer
     */
    public function submitAnswer(Request $request, $id)
    {
        $question = QuestionAndAnswer::findOrFail($id);

        if ($question->assigned_mufti_id !== Auth::id()) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'summary'     => 'required|string|min:10',
            'full_answer' => 'required|string|min:30',
            'references'  => 'nullable|array',
        ]);

        $question->update([
            'title'        => $request->summary,
            'draft_answer' => $request->full_answer,
            'status'       => 'answered',
        ]);

        // References sync
        if ($request->has('references')) {
            $question->draftReferences()->delete();
            foreach ($request->references as $index => $ref) {
                if (!empty($ref['arabic_text'])) {
                    $question->draftReferences()->create([
                        'arabic_text' => $ref['arabic_text'],
                        'book_name'   => $ref['book_name'] ?? '',
                        'language'    => $ref['language'] ?? 'Arabic',
                        'sort_order'  => $index,
                        'is_draft'    => true,
                    ]);
                }
            }
        }

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
