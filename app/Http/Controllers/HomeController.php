<?php

namespace App\Http\Controllers;

use App\Models\Fatwa;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactFormMail;
use App\Models\User;
use App\Models\PortalVisitorLog;
use App\Services\FeaturedFatwaService;
use App\Helpers\HijriHelper;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $stats = \Illuminate\Support\Facades\Cache::remember('home_stats', 900, function () {
            return [
                'total'     => Question::count(),
                'published' => Fatwa::count(),
                'muftis'    => \App\Models\User::role('mufti')->count(),
                'visitors'  => Fatwa::sum('views') + 1250,
            ];
        });

        $latestFatwas = \Illuminate\Support\Facades\Cache::remember('latest_fatwas', 1800, function () {
            return Fatwa::with(['question.madhab', 'question.category', 'muftiAnswer.mufti'])
                                 ->latest('published_at')
                                 ->take(6)
                                 ->get();
        });

        // Detect visitor country from IP via PortalVisitorLog
        $visitorIp      = $request->ip();
        $visitorCountry = PortalVisitorLog::where('ip_address', $visitorIp)
            ->whereNotNull('country_code')
            ->latest()
            ->value('country_code') ?? '';

        // Get current Hijri date
        $hijriParts = explode('-', HijriHelper::now('n-j')); // month-day
        $hijriMonth = (int) ($hijriParts[0] ?? 0);
        $hijriDay   = (int) ($hijriParts[1] ?? 0);

        // Resolve featured fatwas using smart rules (graceful fallback on DB error)
        try {
            $featuredData = (new FeaturedFatwaService())->resolve($visitorCountry, $hijriMonth, $hijriDay);
        } catch (\Exception $e) {
            // Table may not exist yet — hide the section
            $featuredData = ['label' => '', 'sub' => '', 'fatwas' => collect(), 'rule' => null];
        }

        return view('home', compact('stats', 'latestFatwas', 'featuredData'));
    }

    public function contact()
    {
        return view('contact');
    }

    public function submitContact(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => 'required|email|max:255',
            'message'    => 'required|string|min:10',
        ]);

        try {
            Mail::to('darulifta.kauzariyya@gmail.com')->send(new ContactFormMail($validated));
            return back()->with('success', 'Your message has been sent successfully. We will contact you soon.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send message: ' . $e->getMessage())->withInput();
        }
    }

    public function team()
    {
        $muftis = User::role('mufti')
            ->where('is_active', true)
            ->withCount(['muftiAnswers', 'publishedFatwas'])
            // Primary:  explicit sort_order (0 = auto, pushed to end of pinned group)
            // Secondary: most published fatwas first
            ->orderByRaw('CASE WHEN mufti_sort_order = 0 THEN 9999 ELSE mufti_sort_order END ASC')
            ->orderByDesc('published_fatwas_count')
            ->paginate(999);

        return view('team', compact('muftis'));
    }
}
