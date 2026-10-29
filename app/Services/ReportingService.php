<?php

namespace App\Services;

use App\Models\Category;
use App\Models\FatwaActivityLog;
use App\Models\Madhab;
use App\Models\PortalVisitorLog;
use App\Models\QuestionAndAnswer;
use App\Models\QuestionStatusHistory;
use App\Models\User;
use App\Models\UserLoginLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportingService
{
    /**
     * Resolve date boundaries for the selected preset or custom dates.
     */
    public function resolveDateRange(Request $request): array
    {
        $tz = config('app.timezone', 'Asia/Kolkata');
        $now = Carbon::now($tz);
        $dateRange = $request->input('date_range', 'this_month');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        switch ($dateRange) {
            case 'this_month':
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                $label = 'This Month (' . $start->format('M Y') . ')';
                break;
            case 'last_30_days':
                $start = $now->copy()->subDays(29)->startOfDay();
                $end = $now->copy()->endOfDay();
                $label = 'Last 30 Days';
                break;
            case 'last_3_weeks':
                $start = $now->copy()->subDays(20)->startOfDay();
                $end = $now->copy()->endOfDay();
                $label = 'Last 3 Weeks';
                break;
            case 'last_2_weeks':
                $start = $now->copy()->subDays(13)->startOfDay();
                $end = $now->copy()->endOfDay();
                $label = 'Last 2 Weeks';
                break;
            case 'last_1_week':
                $start = $now->copy()->subDays(6)->startOfDay();
                $end = $now->copy()->endOfDay();
                $label = 'Last 1 Week';
                break;
            case 'custom':
                if ($startDate && $endDate) {
                    $start = Carbon::parse($startDate, $tz)->startOfDay();
                    $end = Carbon::parse($endDate, $tz)->endOfDay();
                    $label = 'Custom (' . $start->format('d M Y') . ' – ' . $end->format('d M Y') . ')';
                } else {
                    $start = $now->copy()->startOfMonth();
                    $end = $now->copy()->endOfMonth();
                    $label = 'This Month (' . $start->format('M Y') . ')';
                    $dateRange = 'this_month';
                }
                break;
            case 'today':
                $start = $now->copy()->startOfDay();
                $end = $now->copy()->endOfDay();
                $label = 'Today (' . $start->format('d M Y') . ')';
                break;
            case 'this_week':
                $start = $now->copy()->startOfWeek();
                $end = $now->copy()->endOfWeek();
                $label = 'This Week';
                break;
            case 'this_year':
                $start = $now->copy()->startOfYear();
                $end = $now->copy()->endOfYear();
                $label = 'This Year (' . $start->format('Y') . ')';
                break;
            case 'all':
            default:
                $start = null;
                $end = null;
                $label = 'All Time';
                $dateRange = 'all';
                break;
        }

        // Calculate comparison prior period of equal length
        $prevStart = null;
        $prevEnd = null;
        if ($start && $end) {
            $durationSeconds = max(1, $end->diffInSeconds($start) + 1);
            $prevEnd = $start->copy()->subSecond();
            $prevStart = $prevEnd->copy()->subSeconds($durationSeconds - 1);
        }

        return [
            'date_range'  => $dateRange,
            'start'       => $start,
            'end'         => $end,
            'start_date'  => $start ? $start->format('Y-m-d') : null,
            'end_date'    => $end ? $end->format('Y-m-d') : null,
            'prev_start'  => $prevStart,
            'prev_end'    => $prevEnd,
            'label'       => $label,
            'timezone'    => $tz,
        ];
    }

    /**
     * Calculate 12 summary metrics and comparison changes.
     */
    public function getSummaryMetrics(array $rangeData, array $filters = []): array
    {
        $start = $rangeData['start'];
        $end = $rangeData['end'];
        $prevStart = $rangeData['prev_start'];
        $prevEnd = $rangeData['prev_end'];

        // 1. Total Questions Submitted (Submission Event)
        $subQuery = QuestionAndAnswer::query();
        $this->applyDimensionalFilters($subQuery, $filters);
        if ($start && $end) {
            $subQuery->whereBetween('created_at', [$start, $end]);
        }
        $totalQuestions = $subQuery->count();

        // Previous period submission count for comparison
        $prevTotalQuestions = 0;
        if ($prevStart && $prevEnd) {
            $prevSubQuery = QuestionAndAnswer::query();
            $this->applyDimensionalFilters($prevSubQuery, $filters);
            $prevTotalQuestions = $prevSubQuery->whereBetween('created_at', [$prevStart, $prevEnd])->count();
        }

        // 2. Published Fatwas During Period (Publication Event - REGARDLESS of submission date)
        $pubQuery = QuestionAndAnswer::query()->where('status', 'published');
        $this->applyDimensionalFilters($pubQuery, $filters);
        if ($start && $end) {
            $pubQuery->where(function ($q) use ($start, $end) {
                $q->whereBetween('published_at', [$start, $end])
                  ->orWhere(function ($sq) use ($start, $end) {
                      $sq->whereNull('published_at')->whereBetween('updated_at', [$start, $end]);
                  });
            });
        }
        $publishedFatwas = $pubQuery->count();

        // Previous period publication count for comparison
        $prevPublishedFatwas = 0;
        if ($prevStart && $prevEnd) {
            $prevPubQuery = QuestionAndAnswer::query()->where('status', 'published');
            $this->applyDimensionalFilters($prevPubQuery, $filters);
            $prevPublishedFatwas = $prevPubQuery->where(function ($q) use ($prevStart, $prevEnd) {
                $q->whereBetween('published_at', [$prevStart, $prevEnd])
                  ->orWhere(function ($sq) use ($prevStart, $prevEnd) {
                      $sq->whereNull('published_at')->whereBetween('updated_at', [$prevStart, $prevEnd]);
                  });
            })->count();
        }

        // Publicly accessible records published in period
        $publicPubQuery = (clone $pubQuery)->where('is_public', true);
        $publicPublishedFatwas = $publicPubQuery->count();

        // 3. Current Cohort Status Distribution for questions submitted in period
        $cohortStatusCounts = (clone $subQuery)
            ->selectRaw("
                sum(case when status in ('pending', 'under_review') then 1 else 0 end) as pending_cnt,
                sum(case when status in ('accepted', 'assigned') then 1 else 0 end) as in_progress_cnt,
                sum(case when status in ('answered', 'approved') then 1 else 0 end) as answered_cnt,
                sum(case when status = 'rejected' then 1 else 0 end) as rejected_cnt,
                sum(case when status = 'published' then 1 else 0 end) as published_cohort_cnt
            ")->first();

        $pendingQuestions = (int) ($cohortStatusCounts->pending_cnt ?? 0);
        $inProgressQuestions = (int) ($cohortStatusCounts->in_progress_cnt ?? 0);
        $answeredQuestions = (int) ($cohortStatusCounts->answered_cnt ?? 0);
        $rejectedQuestions = (int) ($cohortStatusCounts->rejected_cnt ?? 0);
        $cohortPublishedQuestions = (int) ($cohortStatusCounts->published_cohort_cnt ?? 0);

        // 4. Public Visitor Analytics (Views & Unique Visitors in period)
        $visitorStats = $this->getVisitorMetrics($start, $end);
        $totalViews = $visitorStats['total_views'];
        $uniqueVisitors = $visitorStats['unique_visitors'];

        // Previous views for comparison
        $prevTotalViews = 0;
        if ($prevStart && $prevEnd) {
            $prevVisitorStats = $this->getVisitorMetrics($prevStart, $prevEnd);
            $prevTotalViews = $prevVisitorStats['total_views'];
        }

        // 5. Active Muftis
        $activeMuftis = User::whereHas('role', fn($q) => $q->where('name', 'mufti'))->where('is_active', true)->count();

        // 6. Work Sessions & Working Time from UserLoginLog
        $sessionStats = $this->getWorkSessionMetrics($start, $end);
        $totalWorkSessions = $sessionStats['total_sessions'];
        $totalWorkingSeconds = $sessionStats['total_seconds'];
        $workingTimeFormatted = $sessionStats['formatted'];
        $workingTimeAvailable = $sessionStats['is_available'];

        // 7. Average Processing Time for fatwas completed/published in period
        $avgProcessingStats = $this->calculateAverageProcessingTime($start, $end, $filters);

        // Percentages & Comparison indicators
        $submissionChange = $this->calcPercentChange($totalQuestions, $prevTotalQuestions);
        $publicationChange = $this->calcPercentChange($publishedFatwas, $prevPublishedFatwas);
        $viewsChange = $this->calcPercentChange($totalViews, $prevTotalViews);

        // Completion Rate
        $publishRate = $totalQuestions > 0 ? round(($publishedFatwas / $totalQuestions) * 100, 1) : ($publishedFatwas > 0 ? 100 : 0);

        return [
            'total_questions'          => $totalQuestions,
            'published_fatwas'         => $publishedFatwas,
            'public_published_fatwas'  => $publicPublishedFatwas,
            'cohort_published'         => $cohortPublishedQuestions,
            'pending_questions'        => $pendingQuestions,
            'in_progress_questions'    => $inProgressQuestions,
            'answered_questions'       => $answeredQuestions,
            'rejected_questions'       => $rejectedQuestions,
            'total_views'              => $totalViews,
            'unique_visitors'          => $uniqueVisitors,
            'active_muftis'            => $activeMuftis,
            'total_work_sessions'      => $totalWorkSessions,
            'total_working_seconds'    => $totalWorkingSeconds,
            'working_time_formatted'   => $workingTimeFormatted,
            'working_time_available'   => $workingTimeAvailable,
            'avg_processing_hours'     => $avgProcessingStats['avg_hours'],
            'avg_processing_days'      => $avgProcessingStats['avg_days'],
            'avg_processing_label'     => $avgProcessingStats['label'],
            'publish_rate'             => $publishRate,
            'submission_change'        => $submissionChange,
            'publication_change'       => $publicationChange,
            'views_change'             => $viewsChange,
        ];
    }

    /**
     * Detailed status distribution breakdown for the submission cohort.
     */
    public function getStatusDistribution(array $rangeData, array $filters = []): array
    {
        $start = $rangeData['start'];
        $end = $rangeData['end'];

        $query = QuestionAndAnswer::query();
        $this->applyDimensionalFilters($query, $filters);
        if ($start && $end) {
            $query->whereBetween('created_at', [$start, $end]);
        }

        $total = $query->count();

        // Exact status counts for cohort
        $counts = (clone $query)->select('status', DB::raw('count(*) as cnt'))->groupBy('status')->pluck('cnt', 'status')->toArray();

        $publishedCount  = (int) ($counts['published'] ?? 0);
        $pendingCount    = (int) (($counts['pending'] ?? 0) + ($counts['under_review'] ?? 0));
        $assignedCount   = (int) ($counts['assigned'] ?? 0);
        $answeredCount   = (int) (($counts['answered'] ?? 0) + ($counts['approved'] ?? 0));
        $rejectedCount   = (int) ($counts['rejected'] ?? 0);

        $categories = [
            [
                'code'        => 'published',
                'label'       => 'Published Fatwas',
                'count'       => $publishedCount,
                'pct'         => $total > 0 ? round(($publishedCount / $total) * 100, 1) : 0,
                'color'       => '#16a34a',
                'bg'          => '#ecfdf5',
                'icon'        => 'bi-journal-check',
                'description' => 'Completed rulings published in the public archive'
            ],
            [
                'code'        => 'pending',
                'label'       => 'Pending Review',
                'count'       => $pendingCount,
                'pct'         => $total > 0 ? round(($pendingCount / $total) * 100, 1) : 0,
                'color'       => '#d97706',
                'bg'          => '#fffbeb',
                'icon'        => 'bi-clock-history',
                'description' => 'Awaiting admin triage and scholar assignment'
            ],
            [
                'code'        => 'assigned',
                'label'       => 'Assigned / In Progress',
                'count'       => $assignedCount,
                'pct'         => $total > 0 ? round(($assignedCount / $total) * 100, 1) : 0,
                'color'       => '#2563eb',
                'bg'          => '#eff6ff',
                'icon'        => 'bi-person-gear',
                'description' => 'Under active study and draft preparation by assigned Mufti'
            ],
            [
                'code'        => 'answered',
                'label'       => 'Answered / Ready for Approval',
                'count'       => $answeredCount,
                'pct'         => $total > 0 ? round(($answeredCount / $total) * 100, 1) : 0,
                'color'       => '#0891b2',
                'bg'          => '#ecfeff',
                'icon'        => 'bi-check2-circle',
                'description' => 'Draft completed and awaiting final review or verification'
            ],
            [
                'code'        => 'rejected',
                'label'       => 'Rejected / Ineligible',
                'count'       => $rejectedCount,
                'pct'         => $total > 0 ? round(($rejectedCount / $total) * 100, 1) : 0,
                'color'       => '#dc2626',
                'bg'          => '#fef2f2',
                'icon'        => 'bi-x-circle',
                'description' => 'Ineligible, duplicate, or non-jurisprudential inquiries'
            ],
        ];

        return [
            'total_cohort' => $total,
            'categories'   => $categories,
        ];
    }

    /**
     * Mufti Performance Matrix with transparent scoring methodology.
     */
    public function getMuftiPerformanceMatrix(array $rangeData, array $filters = []): array
    {
        $start = $rangeData['start'];
        $end = $rangeData['end'];

        $muftiQuery = User::whereHas('role', fn($q) => $q->where('name', 'mufti'))->with('madhab');
        if (!empty($filters['mufti_id'])) {
            $muftiQuery->where('id', $filters['mufti_id']);
        }
        $muftis = $muftiQuery->orderBy('name')->get();

        // 1. Logins per Mufti in period
        $loginCounts = [];
        $lastLogins = [];
        $loginQuery = UserLoginLog::query();
        if ($start && $end) {
            $loginQuery->whereBetween('login_at', [$start, $end]);
        }
        $loginRows = $loginQuery->select('user_id', DB::raw('count(*) as cnt, max(login_at) as last_login'))
            ->groupBy('user_id')
            ->get();
        foreach ($loginRows as $row) {
            $loginCounts[$row->user_id] = (int) $row->cnt;
            $lastLogins[$row->user_id] = $row->last_login ? Carbon::parse($row->last_login) : null;
        }

        // Check fallback logins in FatwaActivityLog (scholar_login, google_login, otp_login)
        $actLoginQuery = FatwaActivityLog::whereIn('event', ['scholar_login', 'google_login', 'otp_login']);
        if ($start && $end) {
            $actLoginQuery->whereBetween('created_at', [$start, $end]);
        }
        $actLoginRows = $actLoginQuery->select('user_id', DB::raw('count(*) as cnt, max(created_at) as last_act'))
            ->groupBy('user_id')
            ->get();
        foreach ($actLoginRows as $row) {
            $current = $loginCounts[$row->user_id] ?? 0;
            if ($row->cnt > $current) {
                $loginCounts[$row->user_id] = (int) $row->cnt;
            }
            if (!$lastLogins[$row->user_id] && $row->last_act) {
                $lastLogins[$row->user_id] = Carbon::parse($row->last_act);
            }
        }

        // 2. Questions Assigned in period (from status history or question table)
        $assignedCounts = [];
        $assignedHistory = QuestionStatusHistory::whereNotNull('new_assigned_mufti_id');
        if ($start && $end) {
            $assignedHistory->whereBetween('created_at', [$start, $end]);
        }
        $assignedHistRows = $assignedHistory->select('new_assigned_mufti_id', DB::raw('count(distinct question_and_answer_id) as cnt'))
            ->groupBy('new_assigned_mufti_id')
            ->pluck('cnt', 'new_assigned_mufti_id')
            ->toArray();
        foreach ($assignedHistRows as $mId => $cnt) {
            $assignedCounts[$mId] = (int) $cnt;
        }

        // Fallback for direct assignments created in period
        $directAssigned = QuestionAndAnswer::whereNotNull('assigned_mufti_id');
        if ($start && $end) {
            $directAssigned->whereBetween('created_at', [$start, $end]);
        }
        $directRows = $directAssigned->select('assigned_mufti_id', DB::raw('count(*) as cnt'))
            ->groupBy('assigned_mufti_id')
            ->pluck('cnt', 'assigned_mufti_id')
            ->toArray();
        foreach ($directRows as $mId => $cnt) {
            $assignedCounts[$mId] = max($assignedCounts[$mId] ?? 0, (int) $cnt);
        }

        // 3. Questions Worked On (Distinct questions actively touched in period)
        $workedOnCounts = [];
        $actWorkQuery = FatwaActivityLog::whereIn('event', ['draft_saved', 'answer_submitted', 'mufti_opened'])
            ->whereNotNull('question_id');
        if ($start && $end) {
            $actWorkQuery->whereBetween('created_at', [$start, $end]);
        }
        $workedRows = $actWorkQuery->select('user_id', DB::raw('count(distinct question_id) as cnt'))
            ->groupBy('user_id')
            ->pluck('cnt', 'user_id')
            ->toArray();
        foreach ($workedRows as $mId => $cnt) {
            $workedOnCounts[$mId] = (int) $cnt;
        }

        // 4. Answers Submitted in period
        $answeredCounts = [];
        $ansActQuery = FatwaActivityLog::where('event', 'answer_submitted');
        if ($start && $end) {
            $ansActQuery->whereBetween('created_at', [$start, $end]);
        }
        $ansRows = $ansActQuery->select('user_id', DB::raw('count(distinct question_id) as cnt'))
            ->groupBy('user_id')
            ->pluck('cnt', 'user_id')
            ->toArray();
        foreach ($ansRows as $mId => $cnt) {
            $answeredCounts[$mId] = (int) $cnt;
        }

        // 5. Fatwas Published in period attributable to Mufti
        $pubCounts = [];
        $pubQuery = QuestionAndAnswer::where('status', 'published')->whereNotNull('assigned_mufti_id');
        if ($start && $end) {
            $pubQuery->where(function ($q) use ($start, $end) {
                $q->whereBetween('published_at', [$start, $end])
                  ->orWhere(function ($sq) use ($start, $end) {
                      $sq->whereNull('published_at')->whereBetween('updated_at', [$start, $end]);
                  });
            });
        }
        $pubRows = $pubQuery->select('assigned_mufti_id', DB::raw('count(*) as cnt, sum(coalesce(views, 0)) as total_views, sum(coalesce(likes, 0)) as total_likes'))
            ->groupBy('assigned_mufti_id')
            ->get()
            ->keyBy('assigned_mufti_id');

        // 6. Current Pending Work (Awaiting action)
        $pendingWorkRows = QuestionAndAnswer::whereNotNull('assigned_mufti_id')
            ->whereIn('status', ['assigned', 'under_review', 'correction'])
            ->select('assigned_mufti_id', DB::raw('count(*) as cnt'))
            ->groupBy('assigned_mufti_id')
            ->pluck('cnt', 'assigned_mufti_id')
            ->toArray();

        // 7. Sessions and Working Time durations
        $sessionDurations = [];
        $sessionCounts = [];
        $sessQuery = UserLoginLog::query();
        if ($start && $end) {
            $sessQuery->whereBetween('login_at', [$start, $end]);
        }
        $sessRows = $sessQuery->select('user_id', DB::raw('count(*) as cnt, sum(duration_seconds) as total_dur'))
            ->groupBy('user_id')
            ->get();
        foreach ($sessRows as $row) {
            $sessionCounts[$row->user_id] = (int) $row->cnt;
            $sessionDurations[$row->user_id] = (int) $row->total_dur;
        }

        // Build Mufti Matrix Collection
        $matrix = $muftis->map(function ($m) use (
            $loginCounts, $lastLogins, $assignedCounts, $workedOnCounts,
            $answeredCounts, $pubRows, $pendingWorkRows, $sessionCounts, $sessionDurations,
            $start, $end
        ) {
            $mId = $m->id;
            $loginCount = (int) ($loginCounts[$mId] ?? 0);
            $lastLogin = $lastLogins[$mId] ?? null;
            $assigned = (int) ($assignedCounts[$mId] ?? 0);
            $workedOn = (int) ($workedOnCounts[$mId] ?? 0);
            $answered = (int) ($answeredCounts[$mId] ?? 0);

            $pubRow = $pubRows->get($mId);
            $published = (int) ($pubRow->cnt ?? 0);
            $views = (int) ($pubRow->total_views ?? 0);
            $likes = (int) ($pubRow->total_likes ?? 0);

            $pendingWork = (int) ($pendingWorkRows[$mId] ?? 0);
            $workSessions = (int) ($sessionCounts[$mId] ?? ($loginCount ?: 0));
            $workingSeconds = (int) ($sessionDurations[$mId] ?? 0);

            // Calculate scholar's average processing time
            $avgTime = $this->calculateMuftiAverageProcessingTime($mId, $start, $end);

            // Calculate Performance Score (0 - 100)
            $scoreData = $this->calculatePerformanceScore($assigned, $workedOn, $answered, $published, $loginCount, $workSessions);

            return (object) [
                'id'                 => $mId,
                'name'               => $m->despl_name ?: $m->name,
                'email'              => $m->email,
                'avatar'             => $m->avatar,
                'madhab_name'        => $m->madhab?->name ?? 'General',
                'is_active'          => (bool) $m->is_active,
                'login_count'        => $loginCount,
                'last_login'         => $lastLogin ? $lastLogin->format('d M Y, h:i A') : '—',
                'questions_assigned' => $assigned,
                'questions_worked'   => max($workedOn, min($assigned, $answered + $published)),
                'answers_submitted'  => $answered,
                'fatwas_published'   => $published,
                'pending_work'       => $pendingWork,
                'total_sessions'     => $workSessions,
                'working_time'       => $workingSeconds > 0 ? sprintf('%dh %02dm', floor($workingSeconds / 3600), round(($workingSeconds % 3600) / 60)) : 'Not Available',
                'working_seconds'    => $workingSeconds,
                'avg_processing'     => $avgTime['label'],
                'performance_score'  => $scoreData['score'],
                'score_tier'         => $scoreData['tier'],
                'score_color'        => $scoreData['color'],
                'total_views'        => $views,
                'total_likes'        => $likes,
            ];
        })->sortByDesc('performance_score')->values();

        return $matrix->toArray();
    }

    /**
     * Transparent, documented scoring formula.
     * Components:
     * - Fatwas Published (35 pts): 4+ fatwas = 35 pts
     * - Answers Submitted (30 pts): 5+ answers = 30 pts
     * - Workload Completion (20 pts): answered+published relative to assigned
     * - Engagement & Availability (15 pts): logins/sessions (3+ logins = 15 pts)
     */
    public function calculatePerformanceScore(int $assigned, int $workedOn, int $answered, int $published, int $logins, int $sessions): array
    {
        // 1. Publication Component (Max 35)
        $pubScore = min(35, round(($published / 4) * 35));

        // 2. Answers Submitted Component (Max 30)
        $ansScore = min(30, round(($answered / 5) * 30));

        // 3. Workload Completion Component (Max 20)
        if ($assigned > 0) {
            $completionRate = min(1.0, ($answered + $published) / $assigned);
            $compScore = round($completionRate * 20);
        } else {
            $compScore = ($published > 0 || $answered > 0) ? 20 : 0;
        }

        // 4. Platform Engagement & Session Activity (Max 15)
        $engScore = min(15, round((max($logins, $sessions) / 3) * 15));

        $totalScore = (int) max(0, min(100, $pubScore + $ansScore + $compScore + $engScore));

        if ($totalScore >= 85) {
            $tier = 'Exceptional';
            $color = '#15803d'; // Green
        } elseif ($totalScore >= 65) {
            $tier = 'High Impact';
            $color = '#0284c7'; // Blue
        } elseif ($totalScore >= 45) {
            $tier = 'Active Contributor';
            $color = '#d97706'; // Amber
        } elseif ($totalScore >= 20) {
            $tier = 'Moderate';
            $color = '#64748b'; // Gray
        } else {
            $tier = 'Low / Inactive in Period';
            $color = '#94a3b8'; // Light gray
        }

        return [
            'score'       => $totalScore,
            'pub_points'  => $pubScore,
            'ans_points'  => $ansScore,
            'comp_points' => $compScore,
            'eng_points'  => $engScore,
            'tier'        => $tier,
            'color'       => $color,
        ];
    }

    /**
     * Visitor analytics data and trends.
     */
    public function getVisitorAnalytics(array $rangeData): array
    {
        $start = $rangeData['start'];
        $end = $rangeData['end'];

        // Query views from FatwaActivityLog (event = country_view)
        $query = FatwaActivityLog::where('event', 'country_view');
        if ($start && $end) {
            $query->whereBetween('created_at', [$start, $end]);
        }

        $totalViews = (clone $query)->count();

        // Unique visitor estimation by unique IP extracted from notes
        $notes = (clone $query)->pluck('notes');
        $uniqueIps = [];
        foreach ($notes as $note) {
            if (preg_match('/IP:\s*([0-9a-f.:]+)/i', $note, $m)) {
                $uniqueIps[$m[1]] = true;
            }
        }
        $uniqueVisitors = count($uniqueIps);

        // Daily visitor trends
        $dailyTrends = [];
        $tz = config('app.timezone', 'Asia/Kolkata');
        if ($start && $end) {
            $isMysql = DB::getDriverName() === 'mysql';
            $viewDateExpr = $isMysql ? "DATE(CONVERT_TZ(created_at, '+00:00', '+05:30'))" : "DATE(created_at)";
            $trendRows = (clone $query)
                ->selectRaw("{$viewDateExpr} as view_date, count(*) as views_count")
                ->groupBy('view_date')
                ->orderBy('view_date', 'asc')
                ->pluck('views_count', 'view_date')
                ->toArray();

            $curr = $start->copy();
            while ($curr->lte($end)) {
                $dStr = $curr->format('Y-m-d');
                $dLabel = $curr->format('d M');
                $dailyTrends[] = [
                    'date'  => $dStr,
                    'label' => $dLabel,
                    'views' => (int) ($trendRows[$dStr] ?? 0),
                ];
                $curr->addDay();
            }
        }

        // Top 10 Most-Viewed Published Fatwas in this period
        $topQuestionIds = (clone $query)
            ->whereNotNull('question_id')
            ->select('question_id', DB::raw('count(*) as period_views'))
            ->groupBy('question_id')
            ->orderByDesc('period_views')
            ->limit(10)
            ->pluck('period_views', 'question_id')
            ->toArray();

        $topFatwas = [];
        if (!empty($topQuestionIds)) {
            $fatwaRecords = QuestionAndAnswer::whereIn('id', array_keys($topQuestionIds))
                ->with(['category', 'madhab', 'assignedMufti'])
                ->get()
                ->keyBy('id');

            foreach ($topQuestionIds as $qId => $cnt) {
                if ($rec = $fatwaRecords->get($qId)) {
                    $rec->period_views = $cnt;
                    $topFatwas[] = $rec;
                }
            }
        }

        // Top Countries from event label or notes
        $countryCounts = [];
        $allLabels = (clone $query)->pluck('event_label');
        foreach ($allLabels as $label) {
            if (preg_match('/Read in\s+(.+)$/i', $label, $m)) {
                $country = trim($m[1]);
                $countryCounts[$country] = ($countryCounts[$country] ?? 0) + 1;
            }
        }
        arsort($countryCounts);
        $topCountries = array_slice($countryCounts, 0, 8, true);

        return [
            'total_views'      => $totalViews,
            'unique_visitors'  => max($uniqueVisitors, min($totalViews, 1)),
            'daily_trends'     => $dailyTrends,
            'top_fatwas'       => $topFatwas,
            'top_countries'    => $topCountries,
        ];
    }

    /**
     * Timeline charts for Submissions, Publications, and Readership.
     */
    public function getLifecycleTrends(array $rangeData): array
    {
        $start = $rangeData['start'];
        $end = $rangeData['end'];

        // If range is month or smaller, show daily trend. Otherwise monthly trend.
        $isDaily = $start && $end && $end->diffInDays($start) <= 45;

        if ($isDaily) {
            $isMysql = DB::getDriverName() === 'mysql';
            $subDateExpr = $isMysql ? "DATE(CONVERT_TZ(created_at, '+00:00', '+05:30'))" : "DATE(created_at)";
            $pubDateExpr = $isMysql ? "DATE(CONVERT_TZ(coalesce(published_at, updated_at), '+00:00', '+05:30'))" : "DATE(coalesce(published_at, updated_at))";

            $subQuery = QuestionAndAnswer::whereBetween('created_at', [$start, $end])
                ->selectRaw("{$subDateExpr} as dt, count(*) as cnt")
                ->groupBy('dt')
                ->pluck('cnt', 'dt')
                ->toArray();

            $pubQuery = QuestionAndAnswer::where('status', 'published')
                ->where(function ($q) use ($start, $end) {
                    $q->whereBetween('published_at', [$start, $end])
                      ->orWhere(function ($sq) use ($start, $end) {
                          $sq->whereNull('published_at')->whereBetween('updated_at', [$start, $end]);
                      });
                })
                ->selectRaw("{$pubDateExpr} as dt, count(*) as cnt")
                ->groupBy('dt')
                ->pluck('cnt', 'dt')
                ->toArray();

            $labels = [];
            $subCounts = [];
            $pubCounts = [];

            $curr = $start->copy();
            while ($curr->lte($end)) {
                $dStr = $curr->format('Y-m-d');
                $labels[] = $curr->format('d M');
                $subCounts[] = (int) ($subQuery[$dStr] ?? 0);
                $pubCounts[] = (int) ($pubQuery[$dStr] ?? 0);
                $curr->addDay();
            }

            return [
                'type'             => 'daily',
                'labels'           => $labels,
                'submitted_counts' => $subCounts,
                'published_counts' => $pubCounts,
            ];
        }

        // Monthly 12-month Trend
        $monthlyData = QuestionAndAnswer::selectRaw("
                DATE_FORMAT(created_at, '%b %Y') as month_year,
                DATE_FORMAT(created_at, '%Y-%m') as sort_key,
                count(*) as total,
                sum(case when status = 'published' then 1 else 0 end) as published
            ")
            ->where('created_at', '>=', Carbon::now()->subMonths(11)->startOfMonth())
            ->groupBy('month_year', 'sort_key')
            ->orderBy('sort_key', 'asc')
            ->get();

        return [
            'type'             => 'monthly',
            'labels'           => $monthlyData->pluck('month_year')->toArray(),
            'submitted_counts' => $monthlyData->pluck('total')->toArray(),
            'published_counts' => $monthlyData->pluck('published')->toArray(),
            'monthly_table'    => $monthlyData,
        ];
    }

    /**
     * Calculate average question processing duration.
     */
    protected function calculateAverageProcessingTime(?Carbon $start, ?Carbon $end, array $filters = []): array
    {
        $query = QuestionAndAnswer::where('status', 'published')
            ->whereNotNull('created_at');
        
        $this->applyDimensionalFilters($query, $filters);

        if ($start && $end) {
            $query->where(function ($q) use ($start, $end) {
                $q->whereBetween('published_at', [$start, $end])
                  ->orWhere(function ($sq) use ($start, $end) {
                      $sq->whereNull('published_at')->whereBetween('updated_at', [$start, $end]);
                  });
            });
        }

        $records = $query->take(500)->get(['created_at', 'published_at', 'updated_at']);
        $totalHours = 0;
        $count = 0;

        foreach ($records as $r) {
            $pubTime = $r->published_at ?? $r->updated_at;
            if ($r->created_at && $pubTime) {
                $diff = $r->created_at->diffInHours($pubTime);
                if ($diff >= 0 && $diff < 87600) { // under 10 years
                    $totalHours += $diff;
                    $count++;
                }
            }
        }

        if ($count === 0) {
            return [
                'avg_hours' => 0,
                'avg_days'  => 0,
                'label'     => '—',
            ];
        }

        $avgHours = round($totalHours / $count, 1);
        $avgDays = round($avgHours / 24, 1);

        $label = $avgDays >= 1 ? "{$avgDays} days" : "{$avgHours} hrs";

        return [
            'avg_hours' => $avgHours,
            'avg_days'  => $avgDays,
            'label'     => $label,
        ];
    }

    /**
     * Average processing time for an individual Mufti.
     */
    protected function calculateMuftiAverageProcessingTime(int $muftiId, ?Carbon $start, ?Carbon $end): array
    {
        $query = QuestionAndAnswer::where('assigned_mufti_id', $muftiId)
            ->where('status', 'published')
            ->whereNotNull('created_at');

        if ($start && $end) {
            $query->where(function ($q) use ($start, $end) {
                $q->whereBetween('published_at', [$start, $end])
                  ->orWhere(function ($sq) use ($start, $end) {
                      $sq->whereNull('published_at')->whereBetween('updated_at', [$start, $end]);
                  });
            });
        }

        $records = $query->take(200)->get(['created_at', 'published_at', 'updated_at']);
        if ($records->isEmpty()) {
            return ['avg_hours' => 0, 'avg_days' => 0, 'label' => '—'];
        }

        $totalHours = 0;
        $count = 0;
        foreach ($records as $r) {
            $pub = $r->published_at ?? $r->updated_at;
            if ($r->created_at && $pub) {
                $diff = $r->created_at->diffInHours($pub);
                if ($diff >= 0 && $diff < 87600) {
                    $totalHours += $diff;
                    $count++;
                }
            }
        }

        if ($count === 0) return ['avg_hours' => 0, 'avg_days' => 0, 'label' => '—'];

        $avgHours = round($totalHours / $count, 1);
        $avgDays = round($avgHours / 24, 1);

        return [
            'avg_hours' => $avgHours,
            'avg_days'  => $avgDays,
            'label'     => $avgDays >= 1 ? "{$avgDays}d" : "{$avgHours}h",
        ];
    }

    /**
     * Work sessions and duration from UserLoginLog.
     */
    protected function getWorkSessionMetrics(?Carbon $start, ?Carbon $end): array
    {
        $query = UserLoginLog::query();
        if ($start && $end) {
            $query->whereBetween('login_at', [$start, $end]);
        }

        $totalSessions = $query->count();
        $totalSeconds = (int) $query->sum('duration_seconds');

        // Check if duration tracking is non-zero
        if ($totalSeconds > 0) {
            $hours = floor($totalSeconds / 3600);
            $mins = round(($totalSeconds % 3600) / 60);
            $formatted = "{$hours}h {$mins}m";
            $isAvailable = true;
        } else {
            $formatted = '0m (Realtime Active)';
            $isAvailable = false; // Disclose that historical interval was not accumulated
        }

        return [
            'total_sessions' => $totalSessions,
            'total_seconds'  => $totalSeconds,
            'formatted'      => $formatted,
            'is_available'   => $isAvailable,
        ];
    }

    /**
     * Visitor reads from FatwaActivityLog.
     */
    protected function getVisitorMetrics(?Carbon $start, ?Carbon $end): array
    {
        $query = FatwaActivityLog::where('event', 'country_view');
        if ($start && $end) {
            $query->whereBetween('created_at', [$start, $end]);
        }

        $totalViews = $query->count();

        // Unique IPs from notes
        $notes = (clone $query)->pluck('notes');
        $uniqueIps = [];
        foreach ($notes as $note) {
            if (preg_match('/IP:\s*([0-9a-f.:]+)/i', $note, $m)) {
                $uniqueIps[$m[1]] = true;
            }
        }
        $uniqueVisitors = count($uniqueIps);

        return [
            'total_views'     => $totalViews,
            'unique_visitors' => max($uniqueVisitors, min($totalViews, 1)),
        ];
    }

    /**
     * Calculate percentage growth or decline.
     */
    protected function calcPercentChange(int $current, int $previous): array
    {
        if ($previous <= 0) {
            return [
                'has_change' => false,
                'pct'        => 0,
                'direction'  => 'neutral',
                'label'      => 'No prior baseline',
            ];
        }

        $diff = $current - $previous;
        $pct = round(($diff / $previous) * 100, 1);

        return [
            'has_change' => true,
            'pct'        => abs($pct),
            'direction'  => $pct > 0 ? 'up' : ($pct < 0 ? 'down' : 'flat'),
            'label'      => ($pct >= 0 ? "+{$pct}%" : "{$pct}%") . ' vs prior period',
        ];
    }

    /**
     * Apply optional dimensional filters (madhab, category, mufti).
     */
    protected function applyDimensionalFilters($query, array $filters = []): void
    {
        if (!empty($filters['madhab_id'])) {
            $query->where('madhab_id', $filters['madhab_id']);
        }
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }
        if (!empty($filters['mufti_id'])) {
            $query->where('assigned_mufti_id', $filters['mufti_id']);
        }
    }
}

