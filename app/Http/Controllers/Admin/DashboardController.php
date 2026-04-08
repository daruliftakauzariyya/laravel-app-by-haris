<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fatwa;
use App\Models\Question;
use App\Models\User;
use App\Models\Category;
use App\Models\PortalVisitorLog;
use Illuminate\Support\Facades\DB;

use App\Models\SiteSetting;
use App\Helpers\HijriHelper;
use Illuminate\Http\Request;

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

        // Consolidated Geographic Logic for Dashboard (Top 5)
        $topCountries = PortalVisitorLog::whereNotNull('country')
            ->where('country', '!=', 'Unknown')
            ->select('country', 'country_code', DB::raw('count(*) as count'))
            ->groupBy('country', 'country_code')
            ->orderByDesc('count')
            ->take(5)
            ->get();

        $geoReach = [];
        foreach ($topCountries as $c) {
            // Get top region for this country
            $topRegion = PortalVisitorLog::where('country', $c->country)
                ->whereNotNull('region')
                ->where('region', '!=', '')
                ->select('region', DB::raw('count(*) as r_count'))
                ->groupBy('region')
                ->orderByDesc('r_count')
                ->first();

            $geoReach[] = [
                'country_code' => $c->country_code,
                'country'      => $c->country,
                'total_count'  => $c->count,
                'top_region'   => $topRegion ? $topRegion->region : 'Unknown',
                'region_count' => $topRegion ? $topRegion->r_count : 0
            ];
        }

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
            'topCategory', 'geoReach',
            'monthlyLabels', 'monthlyCounts'
        ));
    }

    /**
     * Display a comprehensive listing of all global visitors (The Sprite Page)
     */
    public function visitorInsights()
    {
        // Full Country List
        $countries = PortalVisitorLog::whereNotNull('country')
            ->select('country', 'country_code', DB::raw('count(*) as count'))
            ->groupBy('country', 'country_code')
            ->orderByDesc('count')
            ->paginate(20, ['*'], 'country_page');

        // Full Region/State List
        $regions = PortalVisitorLog::whereNotNull('region')
            ->where('region', '!=', '')
            ->select('country', 'region', DB::raw('count(*) as count'))
            ->groupBy('country', 'region')
            ->orderByDesc('count')
            ->paginate(20, ['*'], 'region_page');

        // Full City List
        $cities = PortalVisitorLog::whereNotNull('city')
            ->where('city', '!=', '')
            ->select('country', 'region', 'city', DB::raw('count(*) as count'))
            ->groupBy('country', 'region', 'city')
            ->orderByDesc('count')
            ->paginate(20, ['*'], 'city_page');

        return view('admin.analytics.visitors', compact('countries', 'regions', 'cities'));
    }

    /**
     * Display System Control Panel
     */
    public function systemControl()
    {
        $hijriAdjustment = SiteSetting::get('hijri_adjustment', 0);
        $currentHijri = HijriHelper::now('j F Y');

        return view('admin.settings.system', compact('hijriAdjustment', 'currentHijri'));
    }

    public function updateSettings(Request $request)
    {
        if ($request->has('hijri_adjustment')) {
            SiteSetting::set('hijri_adjustment', $request->hijri_adjustment, 'integer');
        }

        if ($request->has('show_hijri_public')) {
            SiteSetting::set('show_hijri_public', $request->show_hijri_public, 'boolean');
        }

        return back()->with('success', 'Site settings updated successfully.');
    }
}
