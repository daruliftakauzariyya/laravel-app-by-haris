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

        // 2. Executive Key Metrics
        $totalQuestions = (clone $baseQuery)->count();
        $publishedFatwas = (clone $baseQuery)->where('status', 'published')->count();
        $pendingQuestions = (clone $baseQuery)->where('status', 'pending')->count();
        $inProgressQuestions = (clone $baseQuery)->whereIn('status', ['accepted', 'assigned', 'answered'])->count();
        $rejectedQuestions = (clone $baseQuery)->where('status', 'rejected')->count();

        $totalViews = (clone $baseQuery)->where('status', 'published')->sum('views');
        $totalLikes = (clone $baseQuery)->where('status', 'published')->sum('likes');

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

        // 4. Variant 2: Mufti Performance & Workload Matrix
        $muftis = User::whereHas('role', fn($q) => $q->where('name', 'mufti'))->with('madhab')->get()->map(function ($m) use ($dateRange, $startDate, $endDate) {
            $qQuery = QuestionAndAnswer::where('assigned_mufti_id', $m->id);
            if ($dateRange === 'today') {
                $qQuery->whereDate('created_at', Carbon::today());
            } elseif ($dateRange === 'this_month') {
                $qQuery->whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year);
            } elseif ($dateRange === 'custom' && $startDate && $endDate) {
                $qQuery->whereBetween('created_at', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()]);
            }

            $assigned = (clone $qQuery)->count();
            $answered = (clone $qQuery)->whereNotNull('answer')->count();
            $published = (clone $qQuery)->where('status', 'published')->count();
            $views = (clone $qQuery)->where('status', 'published')->sum('views');
            $likes = (clone $qQuery)->where('status', 'published')->sum('likes');

            $m->stat_assigned = $assigned;
            $m->stat_answered = $answered;
            $m->stat_published = $published;
            $m->stat_views = $views;
            $m->stat_likes = $likes;
            $m->completion_rate = $assigned > 0 ? round(($published / $assigned) * 100, 1) : ($published > 0 ? 100 : 0);

            return $m;
        })->sortByDesc('stat_published');

        // 5. Variant 3: Madhab & Islamic Jurisprudence Distribution
        $madhabStats = Madhab::all()->map(function ($md) use ($baseQuery) {
            $count = (clone $baseQuery)->where('madhab_id', $md->id)->count();
            $published = (clone $baseQuery)->where('madhab_id', $md->id)->where('status', 'published')->count();
            $views = (clone $baseQuery)->where('madhab_id', $md->id)->where('status', 'published')->sum('views');
            return (object)[
                'id' => $md->id,
                'name' => $md->name,
                'total' => $count,
                'published' => $published,
                'views' => $views,
            ];
        });

        // 6. Variant 4: Category & Topic Popularity
        $categoryStats = Category::withCount(['questions' => function ($q) use ($dateRange, $startDate, $endDate) {
            if ($dateRange === 'today') {
                $q->whereDate('created_at', Carbon::today());
            } elseif ($dateRange === 'this_month') {
                $q->whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year);
            }
        }])
        ->orderByDesc('questions_count')
        ->limit(10)
        ->get();

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
