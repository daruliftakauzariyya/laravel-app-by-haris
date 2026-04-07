<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fatwa;
use App\Models\Question;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Core stats
        $stats = [
            'total'     => Question::count(),
            'pending'   => Question::where('status', 'pending')->count(),
            'assigned'  => Question::where('status', 'assigned')->count(),
            'answered'  => Question::where('status', 'answered')->count(),
            'published' => Fatwa::count(),
            'total_reads' => (int) Fatwa::sum('views'),
            'questioners' => User::whereHas('questions')->count(),
        ];

        // Most popular category (by published fatwas)
        $topCategory = Category::select('categories.*')
            ->selectRaw('COUNT(questions.id) as q_count')
            ->join('questions', 'categories.id', '=', 'questions.category_id')
            ->where('questions.status', 'published')
            ->groupBy('categories.id')
            ->orderByDesc('q_count')
            ->first();

        // Top 5 countries by questioner count
        $topCountries = User::whereHas('questions')
            ->whereNotNull('country')
            ->where('country', '!=', '')
            ->select('country', DB::raw('count(*) as count'))
            ->groupBy('country')
            ->orderByDesc('count')
            ->take(5)
            ->get();

        // Questions submitted per month (last 6 months)
        $monthlyData = Question::select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        // Build labels for last 6 months
        $monthlyLabels = [];
        $monthlyCounts = [];
        for ($i = 5; $i >= 0; $i--) {
            $key = now()->subMonths($i)->format('Y-m');
            $label = now()->subMonths($i)->format('M Y');
            $monthlyLabels[] = $label;
            $monthlyCounts[] = $monthlyData[$key]->count ?? 0;
        }

        $recentQuestions = Question::with('user')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats', 'recentQuestions',
            'topCategory', 'topCountries',
            'monthlyLabels', 'monthlyCounts'
        ));
    }
}
