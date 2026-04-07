<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    public function index()
    {
        $questions = \App\Models\Question::where('user_id', Auth::id())
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);
                        
        return view('user.dashboard', compact('questions'));
    }
}
