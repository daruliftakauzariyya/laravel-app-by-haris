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

        // Top Countries by visitor count
        $topCountries = PortalVisitorLog::whereNotNull('country')
            ->where('country', '!=', 'Unknown')
            ->select('country', 'country_code', DB::raw('count(*) as count'))
            ->groupBy('country', 'country_code')
            ->orderByDesc('count')
            ->take(5)
            ->get();

        // Top 5 States/Regions
        $topStates = PortalVisitorLog::whereNotNull('region')
            ->where('region', '!=', '')
            ->select('country', 'region', DB::raw('count(*) as count'))
            ->groupBy('country', 'region')
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
            'topCategory', 'topCountries', 'topStates',
            'monthlyLabels', 'monthlyCounts'
        ));
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
