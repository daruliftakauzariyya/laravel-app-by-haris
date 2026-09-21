<?php

namespace App\Http\Controllers\Api\Mufti;

use App\Http\Controllers\Controller;
use App\Models\QuestionAndAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MuftiDashboardApiController extends Controller
{
    /**
     * Unified Scholar Dashboard Data (Synchronized with Web Portal)
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $counts = QuestionAndAnswer::where('assigned_mufti_id', $user->id)
            ->selectRaw('status, count(*) as cnt')
            ->groupBy('status')
            ->pluck('cnt', 'status')
            ->toArray();

        $publishedCount = $counts['published'] ?? 0;
        $answeredCount = ($counts['answered'] ?? 0) + ($counts['approved'] ?? 0) + $publishedCount;
        $totalEver = array_sum($counts);
        
        // Active Queue: All active inquiries assigned to this Mufti that are not yet published
        $assignedCount = $totalEver - $publishedCount;

        $verifiedCount = QuestionAndAnswer::where(function ($q) use ($user) {
            $q->where('checked_approved_by', $user->id)
              ->orWhereHas('statusHistory', fn($h) => $h->where('user_id', $user->id)->where('new_status', 'approved'));
        })->count();

        $score = 0;
        if ($totalEver > 0) {
            $completionRate = ($answeredCount / $totalEver) * 70;
            $speedPoints = 15;
            $volumePoints = min(15, ($answeredCount / 3));
            $score = min(100, (int)round($completionRate + $speedPoints + $volumePoints));
        } else {
            $score = 85;
        }

        $scoreLabel = $score >= 80 ? 'Optimal · Outstanding Throughput' : ($score >= 50 ? 'Steady · Good Flow' : 'Needs Deliberation · Efficiency Index');

        // Fetch all active inquiries ordered by: Pending (1), Opened/Draft (2), Answered (3)
        $urgentTasks = QuestionAndAnswer::with(['category', 'madhab', 'user', 'statusHistory'])
            ->where('assigned_mufti_id', $user->id)
            ->where('status', '!=', 'published')
            ->orderByRaw("CASE 
                WHEN status = 'assigned' AND (draft_answer IS NULL OR draft_answer = '') THEN 1 
                WHEN status = 'assigned' AND (draft_answer IS NOT NULL AND draft_answer != '') THEN 2 
                WHEN status = 'answered' THEN 3 
                ELSE 4 END ASC")
            ->latest('updated_at')
            ->take(15)
            ->get();

        $recentAnswers = QuestionAndAnswer::with(['category', 'madhab'])
            ->where('assigned_mufti_id', $user->id)
            ->whereIn('status', ['answered', 'approved', 'published'])
            ->latest('updated_at')
            ->take(10)
            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => [
                'user' => [
                    'id'                => $user->id,
                    'name'              => $user->name,
                    'email'             => $user->email,
                    'avatar'            => $user->avatar ? asset(ltrim($user->avatar, '/')) : null,
                    'can_check_approve' => (bool)($user->can_check_approve ?? false),
                    'country'           => $user->country ?? 'Global Scholar',
                ],
                'stats' => [
                    'assigned'    => $assignedCount,
                    'pending'     => $assignedCount,
                    'answered'    => $answeredCount,
                    'verified'    => $verifiedCount,
                    'score'       => $score,
                    'score_label' => $scoreLabel,
                ],
                'urgent_tasks'   => $urgentTasks,
                'recent_answers' => $recentAnswers,
            ]
        ]);
    }
}
