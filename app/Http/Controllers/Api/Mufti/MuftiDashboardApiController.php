<?php

namespace App\Http\Controllers\Api\Mufti;

use App\Http\Controllers\Controller;
use App\Models\QuestionAndAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MuftiDashboardApiController extends Controller
{
    /**
     * Unified Scholar Dashboard Data
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $assignedCount = QuestionAndAnswer::where('assigned_mufti_id', $user->id)
            ->where('status', 'assigned')
            ->count();

        $answeredCount = QuestionAndAnswer::where('assigned_mufti_id', $user->id)
            ->whereIn('status', ['answered', 'approved', 'published'])
            ->count();

        $verifiedCount = QuestionAndAnswer::where('checked_by_mufti_id', $user->id)
            ->count();

        $totalEver = QuestionAndAnswer::where('assigned_mufti_id', $user->id)->count();
        $score = 0;
        if ($totalEver > 0) {
            $completionRate = ($answeredCount / $totalEver) * 70;
            $speedPoints = 15;
            $volumePoints = min(15, ($answeredCount / 3));
            $score = min(100, (int)round($completionRate + $speedPoints + $volumePoints));
        } else {
            $score = 85; // Baseline initial index
        }

        $scoreLabel = $score >= 80 ? 'Optimal · Outstanding Throughput' : ($score >= 50 ? 'Steady · Good Flow' : 'Needs Deliberation · Efficiency Index');

        $urgentTasks = QuestionAndAnswer::with(['category', 'madhab', 'user'])
            ->where('assigned_mufti_id', $user->id)
            ->where('status', 'assigned')
            ->latest()
            ->take(5)
            ->get();

        $recentAnswers = QuestionAndAnswer::with(['category', 'madhab'])
            ->where('assigned_mufti_id', $user->id)
            ->whereIn('status', ['answered', 'approved', 'published'])
            ->latest('updated_at')
            ->take(5)
            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => [
                'user' => [
                    'id'                => $user->id,
                    'name'              => $user->name,
                    'email'             => $user->email,
                    'avatar'            => $user->avatar ? asset($user->avatar) : null,
                    'can_check_approve' => (bool)($user->can_check_approve ?? false),
                    'country'           => $user->country ?? 'Global Scholar',
                ],
                'stats' => [
                    'assigned'    => $assignedCount,
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
