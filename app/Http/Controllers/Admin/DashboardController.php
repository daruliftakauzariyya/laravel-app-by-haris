<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fatwa;
use App\Models\Question;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total'     => Question::count(),
            'pending'   => Question::where('status', 'pending')->count(),
            'assigned'  => Question::where('status', 'assigned')->count(),
            'answered'  => Question::where('status', 'answered')->count(),
            'published' => Fatwa::count(),
        ];

        $recentQuestions = Question::with('user')
                                   ->latest()
                                   ->take(5)
                                   ->get();

        return view('admin.dashboard', compact('stats', 'recentQuestions'));
    }
}
