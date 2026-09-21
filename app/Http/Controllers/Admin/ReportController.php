<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuestionAndAnswer;
use App\Models\User;
use App\Models\Category;
use App\Models\Madhab;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Display the comprehensive reporting and analytics center.
     */
    public function index(Request $request)
    {
        $activeTab = $request->input('tab', 'overview');
        $dateRange = $request->input('date_range', 'all');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $selectedMadhab = $request->input('madhab_id');
        $selectedCategory = $request->input('category_id');
        $selectedMufti = $request->input('mufti_id');

        // 1. Build Base Date Filter Query
        $baseQuery = QuestionAndAnswer::query();

        // Apply Date Range
        if ($dateRange === 'today') {
            $baseQuery->whereDate('created_at', Carbon::today());
        } elseif ($dateRange === 'this_week') {
            $baseQuery->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($dateRange === 'this_month') {
            $baseQuery->whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year);
        } elseif ($dateRange === 'last_30_days') {
            $baseQuery->where('created_at', '>=', Carbon::now()->subDays(30));
        } elseif ($dateRange === 'this_year') {
            $baseQuery->whereYear('created_at', Carbon::now()->year);
        } elseif ($dateRange === 'custom' && $startDate && $endDate) {
            $baseQuery->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);
        }

        // Apply Optional Dimensional Filters
        if ($selectedMadhab) {
            $baseQuery->where('madhab_id', $selectedMadhab);
        }
        if ($selectedCategory) {
            $baseQuery->where('category_id', $selectedCategory);
        }
        if ($selectedMufti) {
            $baseQuery->where('assigned_mufti_id', $selectedMufti);
        }

        // 2. Executive Key Metrics (Single fast aggregate query)
        $metrics = (clone $baseQuery)
            ->selectRaw("
                count(*) as total_questions,
                sum(case when status = 'published' then 1 else 0 end) as published_fatwas,
                sum(case when status = 'pending' then 1 else 0 end) as pending_questions,
                sum(case when status in ('accepted', 'assigned', 'answered') then 1 else 0 end) as in_progress_questions,
                sum(case when status = 'rejected' then 1 else 0 end) as rejected_questions,
                sum(case when status = 'published' then coalesce(views, 0) else 0 end) as total_views,
                sum(case when status = 'published' then coalesce(likes, 0) else 0 end) as total_likes
            ")
            ->first();

        $totalQuestions = (int) ($metrics->total_questions ?? 0);
        $publishedFatwas = (int) ($metrics->published_fatwas ?? 0);
        $pendingQuestions = (int) ($metrics->pending_questions ?? 0);
        $inProgressQuestions = (int) ($metrics->in_progress_questions ?? 0);
        $rejectedQuestions = (int) ($metrics->rejected_questions ?? 0);
        $totalViews = (int) ($metrics->total_views ?? 0);
        $totalLikes = (int) ($metrics->total_likes ?? 0);

        $activeMuftisCount = User::whereHas('role', fn($q) => $q->where('name', 'mufti'))->where('is_active', true)->count();
        $registeredQuestionersCount = User::whereHas('role', fn($q) => $q->where('name', 'questioner'))->count();

        // Publication Success Rate (%)
        $publishRate = $totalQuestions > 0 ? round(($publishedFatwas / $totalQuestions) * 100, 1) : 0;

        // 3. Variant 1: Lifecycle & Monthly Timeline Trend
        $monthlyTrend = QuestionAndAnswer::selectRaw("DATE_FORMAT(created_at, '%b %Y') as month_year, DATE_FORMAT(created_at, '%Y-%m') as sort_key, count(*) as total, sum(case when status = 'published' then 1 else 0 end) as published")
            ->where('created_at', '>=', Carbon::now()->subMonths(11)->startOfMonth())
            ->groupBy('month_year', 'sort_key')
            ->orderBy('sort_key', 'asc')
            ->get();

        // Status Breakdown for Chart/Cards
        $statusBreakdown = [
            ['label' => 'Published', 'count' => $publishedFatwas, 'color' => '#16a34a', 'bg' => '#ecfdf5', 'icon' => 'bi-journal-check'],
            ['label' => 'Pending Review', 'count' => $pendingQuestions, 'color' => '#d97706', 'bg' => '#fffbeb', 'icon' => 'bi-clock-history'],
            ['label' => 'In Progress / Answered', 'count' => $inProgressQuestions, 'color' => '#2563eb', 'bg' => '#eff6ff', 'icon' => 'bi-chat-dots'],
            ['label' => 'Rejected', 'count' => $rejectedQuestions, 'color' => '#dc2626', 'bg' => '#fef2f2', 'icon' => 'bi-x-circle'],
        ];

        // 4. Variant 2: Mufti Performance & Workload Matrix (Optimized GROUP BY - no N+1 queries)
        $muftiStatsQuery = QuestionAndAnswer::query();
        if ($dateRange === 'today') {
            $muftiStatsQuery->whereDate('created_at', Carbon::today());
        } elseif ($dateRange === 'this_month') {
            $muftiStatsQuery->whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year);
        } elseif ($dateRange === 'custom' && $startDate && $endDate) {
            $muftiStatsQuery->whereBetween('created_at', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()]);
        }

        $muftiAggregates = $muftiStatsQuery
            ->whereNotNull('assigned_mufti_id')
            ->selectRaw("
                assigned_mufti_id,
                count(*) as stat_assigned,
                sum(case when answer is not null and answer != '' then 1 else 0 end) as stat_answered,
                sum(case when status = 'published' then 1 else 0 end) as stat_published,
                sum(case when status = 'published' then coalesce(views, 0) else 0 end) as stat_views,
                sum(case when status = 'published' then coalesce(likes, 0) else 0 end) as stat_likes
            ")
            ->groupBy('assigned_mufti_id')
            ->get()
            ->keyBy('assigned_mufti_id');

        $muftis = User::whereHas('role', fn($q) => $q->where('name', 'mufti'))
            ->with('madhab')
            ->get()
            ->map(function ($m) use ($muftiAggregates) {
                $agg = $muftiAggregates->get($m->id);
                $assigned = (int) ($agg->stat_assigned ?? 0);
                $answered = (int) ($agg->stat_answered ?? 0);
                $published = (int) ($agg->stat_published ?? 0);
                $views = (int) ($agg->stat_views ?? 0);
                $likes = (int) ($agg->stat_likes ?? 0);

                $m->stat_assigned = $assigned;
                $m->stat_answered = $answered;
                $m->stat_published = $published;
                $m->stat_views = $views;
                $m->stat_likes = $likes;
                $m->completion_rate = $assigned > 0 ? round(($published / $assigned) * 100, 1) : ($published > 0 ? 100 : 0);

                return $m;
            })->sortByDesc('stat_published');

        // 5. Variant 3: Madhab & Islamic Jurisprudence Distribution (Single aggregate query)
        $madhabAggregates = (clone $baseQuery)
            ->whereNotNull('madhab_id')
            ->selectRaw("
                madhab_id,
                count(*) as total,
                sum(case when status = 'published' then 1 else 0 end) as published,
                sum(case when status = 'published' then coalesce(views, 0) else 0 end) as views
            ")
            ->groupBy('madhab_id')
            ->get()
            ->keyBy('madhab_id');

        $madhabStats = Madhab::all()->map(function ($md) use ($madhabAggregates) {
            $agg = $madhabAggregates->get($md->id);
            return (object)[
                'id' => $md->id,
                'name' => $md->name,
                'total' => (int) ($agg->total ?? 0),
                'published' => (int) ($agg->published ?? 0),
                'views' => (int) ($agg->views ?? 0),
            ];
        });

        // 6. Variant 4: Category & Topic Popularity (Direct fast aggregate)
        $topCatCounts = (clone $baseQuery)
            ->whereNotNull('category_id')
            ->selectRaw('category_id, count(*) as questions_count')
            ->groupBy('category_id')
            ->orderByDesc('questions_count')
            ->limit(10)
            ->pluck('questions_count', 'category_id');

        $categoryStats = Category::whereIn('id', $topCatCounts->keys())->get()->map(function ($cat) use ($topCatCounts) {
            $cat->questions_count = (int) ($topCatCounts[$cat->id] ?? 0);
            return $cat;
        })->sortByDesc('questions_count')->values();

        // Top 10 Most Read Published Fatwas
        $topFatwas = QuestionAndAnswer::where('status', 'published')
            ->with(['category', 'madhab', 'assignedMufti'])
            ->orderByDesc('views')
            ->limit(10)
            ->get();

        // 7. Variant 5: Demographics, Language & Geography
        $languageStats = (clone $baseQuery)
            ->select('language', DB::raw('count(*) as count'))
            ->groupBy('language')
            ->orderByDesc('count')
            ->get();

        $countryStats = User::whereNotNull('country')
            ->where('country', '!=', '')
            ->select('country', DB::raw('count(*) as count'))
            ->groupBy('country')
            ->orderByDesc('count')
            ->limit(8)
            ->get();

        $genderStats = User::whereNotNull('gender')
            ->select('gender', DB::raw('count(*) as count'))
            ->groupBy('gender')
            ->get();

        // Reference dropdown options
        $allMadhabs = Madhab::all();
        $allCategories = Category::whereNull('parent_id')->get();
        $allMuftis = User::whereHas('role', fn($q) => $q->where('name', 'mufti'))->get();

        return view('admin.reports.index', compact(
            'activeTab',
            'dateRange',
            'startDate',
            'endDate',
            'selectedMadhab',
            'selectedCategory',
            'selectedMufti',
            'totalQuestions',
            'publishedFatwas',
            'pendingQuestions',
            'inProgressQuestions',
            'rejectedQuestions',
            'totalViews',
            'totalLikes',
            'activeMuftisCount',
            'registeredQuestionersCount',
            'publishRate',
            'monthlyTrend',
            'statusBreakdown',
            'muftis',
            'madhabStats',
            'categoryStats',
            'topFatwas',
            'languageStats',
            'countryStats',
            'genderStats',
            'allMadhabs',
            'allCategories',
            'allMuftis'
        ));
    }

    /**
     * Export report data as a structured CSV spreadsheet.
     */
    public function export(Request $request)
    {
        $dateRange = $request->input('date_range', 'all');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $selectedMadhab = $request->input('madhab_id');
        $selectedCategory = $request->input('category_id');
        $selectedMufti = $request->input('mufti_id');

        $query = QuestionAndAnswer::with(['user', 'assignedMufti', 'category', 'madhab']);

        if ($dateRange === 'today') {
            $query->whereDate('created_at', Carbon::today());
        } elseif ($dateRange === 'this_week') {
            $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($dateRange === 'this_month') {
            $query->whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year);
        } elseif ($dateRange === 'last_30_days') {
            $query->where('created_at', '>=', Carbon::now()->subDays(30));
        } elseif ($dateRange === 'this_year') {
            $query->whereYear('created_at', Carbon::now()->year);
        } elseif ($dateRange === 'custom' && $startDate && $endDate) {
            $query->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);
        }

        if ($selectedMadhab) $query->where('madhab_id', $selectedMadhab);
        if ($selectedCategory) $query->where('category_id', $selectedCategory);
        if ($selectedMufti) $query->where('assigned_mufti_id', $selectedMufti);

        $records = $query->orderByDesc('id')->get();
        $fileName = 'darul_ifta_report_' . Carbon::now()->format('Y-m-d_H-i') . '.csv';

        $response = new StreamedResponse(function () use ($records) {
            $handle = fopen('php://output', 'w');
            // Add UTF-8 BOM for Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // CSV Headers
            fputcsv($handle, [
                'Question ID',
                'Title / Summary',
                'Questioner Name',
                'Questioner Email',
                'Category',
                'Madhab',
                'Assigned Mufti',
                'Language',
                'Status',
                'Views',
                'Likes',
                'Submission Date',
                'Publication Date'
            ]);

            foreach ($records as $row) {
                fputcsv($handle, [
                    $row->question_id,
                    $row->title ?: strip_tags(substr($row->body, 0, 100)),
                    $row->user?->name ?? 'Guest/Anonymous',
                    $row->user?->email ?? '—',
                    $row->category?->name ?? 'Uncategorized',
                    $row->madhab?->name ?? 'General',
                    $row->assignedMufti?->name ?? 'Unassigned',
                    $row->language ?? 'English',
                    ucfirst($row->status),
                    $row->views ?? 0,
                    $row->likes ?? 0,
                    $row->created_at ? $row->created_at->format('Y-m-d H:i') : '—',
                    $row->published_at ? Carbon::parse($row->published_at)->format('Y-m-d H:i') : '—',
                ]);
            }
            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '"');

        return $response;
    }
}
