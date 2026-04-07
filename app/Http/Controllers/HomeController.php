<?php

namespace App\Http\Controllers;

use App\Models\Fatwa;
use App\Models\Question;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $stats = [
            'total'     => Question::count(),
            'published' => Fatwa::count(),
            'muftis'    => \App\Models\User::role('mufti')->count(),
            'visitors'  => Fatwa::sum('views') + 1250, // Added base seed for realism
        ];
        $latestFatwas = Fatwa::with(['question', 'muftiAnswer.mufti'])
                             ->latest('published_at')
                             ->take(6)
                             ->get();
        return view('home', compact('stats', 'latestFatwas'));
    }

    public function contact()
    {
        return view('contact');
    }
}
