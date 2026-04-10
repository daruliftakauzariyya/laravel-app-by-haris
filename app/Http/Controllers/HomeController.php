<?php

namespace App\Http\Controllers;

use App\Models\Fatwa;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactFormMail;
use App\Models\User;

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
            ->orderByDesc('published_fatwas_count')
            ->paginate(8);

        return view('team', compact('muftis'));
    }
}
