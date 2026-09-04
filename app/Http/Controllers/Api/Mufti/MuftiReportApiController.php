<?php

namespace App\Http\Controllers\Api\Mufti;

use App\Http\Controllers\Controller;
use App\Models\QuestionAndAnswer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MuftiReportApiController extends Controller
{
    /**
     * Scholar Reports & Velocity Analytics
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $dateRange = $request->get('date_range', 'all');

        $startDate = match ($dateRange) {
            'today'        => Carbon::today(),
            'this_week'    => Carbon::now()->startOfWeek(),
            'this_month'   => Carbon::now()->startOfMonth(),
            'last_30_days' => Carbon::now()->subDays(30),
            'this_year'    => Carbon::now()->startOfYear(),
            default        => null,
        };

        // Query builders with date range
        $assignedQuery = QuestionAndAnswer::where('assigned_mufti_id', $user->id);
        $answeredQuery = QuestionAndAnswer::where('assigned_mufti_id', $user->id)
            ->whereIn('status', ['answered', 'approved', 'published']);
        $publishedQuery = QuestionAndAnswer::where('assigned_mufti_id', $user->id)
            ->where('status', 'published');
        $pendingQuery = QuestionAndAnswer::where('assigned_mufti_id', $user->id)
            ->where('status', 'assigned');

        if ($startDate) {
            $assignedQuery->where('created_at', '>=', $startDate);
            $answeredQuery->where('updated_at', '>=', $startDate);
            $publishedQuery->where('updated_at', '>=', $startDate);
        }

        $assignedCount  = $assignedQuery->count();
        $answeredCount  = $answeredQuery->count();
        $publishedCount = $publishedQuery->count();
        $pendingCount   = $pendingQuery->count();

        // Monthly publication velocity (past 6 months)
        $monthlyVelocity = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthEnd   = Carbon::now()->subMonths($i)->endOfMonth();
            $monthLabel = $monthStart->format('M Y');

            $count = QuestionAndAnswer::where('assigned_mufti_id', $user->id)
                ->whereIn('status', ['answered', 'approved', 'published'])
                ->whereBetween('updated_at', [$monthStart, $monthEnd])
                ->count();

            $monthlyVelocity[] = [
                'month' => $monthLabel,
                'count' => $count,
            ];
        }

        return response()->json([
            'status' => 'success',
            'data'   => [
                'date_range' => $dateRange,
                'kpis' => [
                    'assigned'  => $assignedCount,
                    'answered'  => $answeredCount,
                    'published' => $publishedCount,
                    'pending'   => $pendingCount,
                ],
                'monthly_velocity' => $monthlyVelocity,
            ]
        ]);
    }
}
