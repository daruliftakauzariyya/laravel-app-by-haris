<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Category;
use App\Models\QuestionAndAnswer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\SiteSetting;
use App\Helpers\HijriHelper;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Core stats
        $stats = [
            'total'     => QuestionAndAnswer::count(),
            'pending'   => QuestionAndAnswer::where('status', 'pending')->count(),
            'assigned'  => QuestionAndAnswer::where('status', 'assigned')->count(),
            'answered'  => QuestionAndAnswer::where('status', 'answered')->count(),
            'published' => QuestionAndAnswer::where('status', 'published')->count(),
            'total_reads' => (int) QuestionAndAnswer::sum('views'),
            'questioners' => User::whereHas('questions')->count(),
            'live_by_ip'  => 0,
            'live_by_identity' => 0,
        ];

        // Most popular category (by published fatwas)
        $topCategory = Category::select('categories.*')
            ->selectRaw('COUNT(question_and_answer.id) as q_count')
            ->join('question_and_answer', 'categories.id', '=', 'question_and_answer.category_id')
            ->where('question_and_answer.status', 'published')
            ->groupBy('categories.id')
            ->orderByDesc('q_count')
            ->first();

        // Consolidated Geographic Logic for Dashboard (Top 5)
        $geoReach = [];

        // Questions submitted per month (last 6 months)
        $monthlyData = QuestionAndAnswer::select(
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

        $recentQuestions = QuestionAndAnswer::with(['user', 'madhab', 'category', 'assignedMufti'])
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
     * Display a comprehensive listing of all global visitors
     */
    public function visitorInsights()
    {
        $countries = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
        $regions = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
        $cities = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);

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

        // Admin Account Update
        if ($request->filled('admin_email') || $request->filled('admin_password')) {
            $request->validate([
                'admin_email'    => ['nullable', 'email', 'unique:users,email,' . Auth::id()],
                'admin_password' => ['nullable', 'string', 'min:8', 'confirmed'],
            ]);

            $user = Auth::user();
            if ($request->filled('admin_email')) {
                $user->email = $request->admin_email;
            }
            if ($request->filled('admin_password')) {
                $user->password = Hash::make($request->admin_password);
            }
            $user->save();

            return back()->with('success', 'Admin login credentials updated successfully.');
        }

        return back()->with('success', 'Site settings updated successfully.');
    }

    /**
     * REST API for real-time live users polling
     */
    public function liveUsersApi()
    {
        return response()->json([
            'byIp' => 0,
            'byIdentity' => 0,
        ]);
    }

    /**
     * System Maintenance: Clear all caches
     */
    public function clearCache()
    {
        try {
            \Illuminate\Support\Facades\Artisan::call('view:clear');
            \Illuminate\Support\Facades\Artisan::call('cache:clear');
            \Illuminate\Support\Facades\Artisan::call('config:clear');
            return back()->with('success', 'System cache cleared successfully. All views are now fresh.');
        } catch (\Exception $e) {
            return back()->with('error', 'Maintenance Error: ' . $e->getMessage());
        }
    }
}
