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
            'answered'  => Question::where('status', 'answered')->orWhere('status', 'published')->count(),
            'published' => Fatwa::count(),
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
