<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function showCompleteForm()
    {
        if (Auth::user()->profile_completed) {
            return redirect()->route('home');
        }
        return view('auth.profile-complete');
    }

    public function complete(Request $request)
    {
        $request->validate([
            'name'            => ['required', 'string', 'max:255'],
            'gender'          => ['required', 'in:male,female'],
            'country'         => ['required', 'string', 'max:100'],
            'mobile'          => ['required', 'string', 'max:20'],
            'mother_language' => ['required', 'string', 'max:50'],
        ]);

        $user = Auth::user();
        $user->update([
            'name'              => $request->name,
            'gender'            => $request->gender,
            'country'           => $request->country,
            'mobile'            => $request->mobile,
            'mother_language'   => $request->mother_language,
            'profile_completed' => true,
        ]);

        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }
        if ($user->hasRole('mufti')) {
            return redirect()->route('mufti.dashboard');
        }
        return redirect()->route('home')->with('success', 'Profile completed successfully!');
    }
}
