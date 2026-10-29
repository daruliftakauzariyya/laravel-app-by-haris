<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Madhab;
use App\Models\QuestionAndAnswer;
use App\Models\User;
use App\Services\ReportingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    protected ReportingService $reportingService;

    public function __construct(ReportingService $reportingService)
    {
        $this->reportingService = $reportingService;
    }

    /**
     * Display the comprehensive reporting and analytics center.
     */
    public function index(Request $request)
    {
        $activeTab = $request->input('tab', 'overview');
        $selectedMadhab = $request->input('madhab_id');
        $selectedCategory = $request->input('category_id');
        $selectedMufti = $request->input('mufti_id');

        $filters = array_filter([
            'madhab_id'   => $selectedMadhab,
            'category_id' => $selectedCategory,
            'mufti_id'    => $selectedMufti,
        ]);

        // 1. Resolve Active Date Boundaries & Comparison Periods
        $rangeData = $this->reportingService->resolveDateRange($request);
        $dateRange = $rangeData['date_range'];
        $startDate = $rangeData['start_date'];
        $endDate = $rangeData['end_date'];
        $periodLabel = $rangeData['label'];

        // 2. Executive Key Metrics
        $metrics = $this->reportingService->getSummaryMetrics($rangeData, $filters);

        // 3. Status Distribution Breakdown (Cohort of submitted inquiries)
        $statusDistribution = $this->reportingService->getStatusDistribution($rangeData, $filters);
        $statusBreakdown = $statusDistribution['categories'];

        // 4. Mufti / Scholar Performance Matrix
        $muftis = $this->reportingService->getMuftiPerformanceMatrix($rangeData, $filters);

        // 5. Public Visitor Analytics & Trends
        $visitorAnalytics = $this->reportingService->getVisitorAnalytics($rangeData);

        // 6. Lifecycle Trends (Daily or Monthly depending on horizon)
        $lifecycleTrends = $this->reportingService->getLifecycleTrends($rangeData);

        // 7. Jurisprudence & Madhab Distribution
        $start = $rangeData['start'];
        $end = $rangeData['end'];

        $madhabAggQuery = QuestionAndAnswer::query();
        if ($start && $end) {
            $madhabAggQuery->whereBetween('created_at', [$start, $end]);
        }
        $madhabAggregates = $madhabAggQuery
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
            return (object) [
                'id'        => $md->id,
                'name'      => $md->name,
                'total'     => (int) ($agg->total ?? 0),
                'published' => (int) ($agg->published ?? 0),
                'views'     => (int) ($agg->views ?? 0),
            ];
        });

        // 8. Top Categories & Inquiry Topics
        $catAggQuery = QuestionAndAnswer::query();
        if ($start && $end) {
            $catAggQuery->whereBetween('created_at', [$start, $end]);
        }
        $topCatCounts = $catAggQuery
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

        // Top 10 Most-Read Published Fatwas
        $topFatwas = $visitorAnalytics['top_fatwas'];
        if (empty($topFatwas)) {
            $topFatwas = QuestionAndAnswer::where('status', 'published')
                ->with(['category', 'madhab', 'assignedMufti'])
                ->orderByDesc('views')
                ->limit(10)
                ->get();
        }

        // 9. Demographics, Geography & Language
        $langQuery = QuestionAndAnswer::query();
        if ($start && $end) {
            $langQuery->whereBetween('created_at', [$start, $end]);
        }
        $languageStats = $langQuery
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
            'periodLabel',
            'rangeData',
            'selectedMadhab',
            'selectedCategory',
            'selectedMufti',
            'metrics',
            'statusBreakdown',
            'statusDistribution',
            'muftis',
            'visitorAnalytics',
            'lifecycleTrends',
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
     * Download comprehensive, printer-ready PDF report for selected reporting period.
     */
    public function downloadPdf(Request $request)
    {
        $selectedMadhab = $request->input('madhab_id');
        $selectedCategory = $request->input('category_id');
        $selectedMufti = $request->input('mufti_id');

        $filters = array_filter([
            'madhab_id'   => $selectedMadhab,
            'category_id' => $selectedCategory,
            'mufti_id'    => $selectedMufti,
        ]);

        $rangeData = $this->reportingService->resolveDateRange($request);
        $periodLabel = $rangeData['label'];
        $startDate = $rangeData['start_date'] ?? 'all';
        $endDate = $rangeData['end_date'] ?? 'time';

        // Filename reflecting selected reporting period
        $fileName = 'darul-ifta-report-' . ($startDate ?: 'all') . '-to-' . ($endDate ?: 'present') . '.pdf';

        $metrics = $this->reportingService->getSummaryMetrics($rangeData, $filters);
        $statusDistribution = $this->reportingService->getStatusDistribution($rangeData, $filters);
        $muftis = $this->reportingService->getMuftiPerformanceMatrix($rangeData, $filters);
        $visitorAnalytics = $this->reportingService->getVisitorAnalytics($rangeData);
        $lifecycleTrends = $this->reportingService->getLifecycleTrends($rangeData);

        $allMadhabs = Madhab::all();
        $allCategories = Category::whereNull('parent_id')->get();

        return view('admin.reports.pdf', compact(
            'rangeData',
            'periodLabel',
            'fileName',
            'metrics',
            'statusDistribution',
            'muftis',
            'visitorAnalytics',
            'lifecycleTrends',
            'allMadhabs',
            'allCategories',
            'filters'
        ));
    }

    /**
     * Export report data as a structured CSV spreadsheet with formula injection defense.
     */
    public function export(Request $request)
    {
        $selectedMadhab = $request->input('madhab_id');
        $selectedCategory = $request->input('category_id');
        $selectedMufti = $request->input('mufti_id');

        $filters = array_filter([
            'madhab_id'   => $selectedMadhab,
            'category_id' => $selectedCategory,
            'mufti_id'    => $selectedMufti,
        ]);

        $rangeData = $this->reportingService->resolveDateRange($request);
        $start = $rangeData['start'];
        $end = $rangeData['end'];

        $query = QuestionAndAnswer::with(['user', 'assignedMufti', 'category', 'madhab']);

        if ($start && $end) {
            $query->where(function ($q) use ($start, $end) {
                // Questions submitted in period OR published in period
                $q->whereBetween('created_at', [$start, $end])
                  ->orWhere(function ($pubQ) use ($start, $end) {
                      $pubQ->where('status', 'published')
                           ->where(function ($sq) use ($start, $end) {
                               $sq->whereBetween('published_at', [$start, $end])
                                  ->orWhere(function ($nsq) use ($start, $end) {
                                      $nsq->whereNull('published_at')->whereBetween('updated_at', [$start, $end]);
                                  });
                           });
                  });
            });
        }

        if ($selectedMadhab) $query->where('madhab_id', $selectedMadhab);
        if ($selectedCategory) $query->where('category_id', $selectedCategory);
        if ($selectedMufti) $query->where('assigned_mufti_id', $selectedMufti);

        $records = $query->orderByDesc('id')->get();
        $fileName = 'darul_ifta_report_' . ($rangeData['start_date'] ?? 'all') . '_to_' . ($rangeData['end_date'] ?? 'present') . '.csv';

        $response = new StreamedResponse(function () use ($records) {
            $handle = fopen('php://output', 'w');
            // Add UTF-8 BOM for Excel / multilingual compatibility
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
                'Is Public',
                'Views',
                'Likes',
                'Submission Date',
                'Publication Date'
            ]);

            foreach ($records as $row) {
                fputcsv($handle, [
                    $this->sanitizeCsvField($row->question_id),
                    $this->sanitizeCsvField($row->title ?: strip_tags(substr($row->body, 0, 100))),
                    $this->sanitizeCsvField($row->user?->name ?? 'Guest/Anonymous'),
                    $this->sanitizeCsvField($row->user?->email ?? '—'),
                    $this->sanitizeCsvField($row->category?->name ?? 'Uncategorized'),
                    $this->sanitizeCsvField($row->madhab?->name ?? 'General'),
                    $this->sanitizeCsvField($row->assignedMufti?->name ?? 'Unassigned'),
                    $this->sanitizeCsvField($row->language ?? 'English'),
                    $this->sanitizeCsvField(ucfirst($row->status)),
                    $row->is_public ? 'Yes' : 'No',
                    $row->views ?? 0,
                    $row->likes ?? 0,
                    $row->created_at ? $row->created_at->format('Y-m-d H:i') : '—',
                    $row->published_at ? Carbon::parse($row->published_at)->format('Y-m-d H:i') : ($row->status === 'published' && $row->updated_at ? $row->updated_at->format('Y-m-d H:i') : '—'),
                ]);
            }
            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '"');

        return $response;
    }

    /**
     * Prevent CSV formula injection by prepending single quote if field starts with formula character.
     */
    protected function sanitizeCsvField(?string $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }
        $first = substr($value, 0, 1);
        if (in_array($first, ['=', '+', '-', '@', "\t", "\r"])) {
            return "'" . $value;
        }
        return $value;
    }
}
