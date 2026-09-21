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

        $countsData = QuestionAndAnswer::where('assigned_mufti_id', $user->id)
            ->selectRaw('status, count(*) as cnt')
            ->groupBy('status')
            ->pluck('cnt', 'status')
            ->toArray();

        $publishedCount = $countsData['published'] ?? 0;
        $totalEver = array_sum($countsData);
        $activeCount = $totalEver - $publishedCount;
        $answeredCount = ($countsData['answered'] ?? 0) + ($countsData['approved'] ?? 0) + $publishedCount;

        $verifiedCount = QuestionAndAnswer::where(function ($q) use ($user) {
            $q->where('checked_approved_by', $user->id)
              ->orWhereHas('statusHistory', fn($h) => $h->where('user_id', $user->id)->where('new_status', 'approved'));
        })->count();

        $kpis = [
            'assigned'  => $activeCount,
            'pending'   => $activeCount,
            'answered'  => $answeredCount,
            'published' => $publishedCount,
            'verified'  => $verifiedCount,
        ];

        return response()->json([
            'status' => 'success',
            'data'   => [
                'kpis' => $kpis,
            ]
        ]);
    }
}
