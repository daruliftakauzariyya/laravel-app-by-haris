<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::where('google_id', $googleUser->getId())
                        ->orWhere('email', $googleUser->getEmail())
                        ->first();

            if ($user) {
                // Update google_id if logging in via Google for the first time
                if (!$user->google_id) {
                    $user->update([
                        'google_id' => $googleUser->getId(),
                        'avatar'    => $googleUser->getAvatar(),
                    ]);
                }
            } else {
                // Create new user
                $user = User::create([
                    'name'              => $googleUser->getName(),
                    'email'             => $googleUser->getEmail(),
                    'google_id'         => $googleUser->getId(),
                    'avatar'            => $googleUser->getAvatar(),
                    'email_verified_at' => now(),
                    'profile_completed' => false,
                ]);
                $user->assignRole('questioner');
            }

            Auth::login($user, true);

            if (!$user->profile_completed) {
                return redirect()->route('profile.complete');
            }

            // Role-based redirect
            if ($user->hasRole('admin')) {
                return redirect()->route('admin.dashboard');
            }
            if ($user->hasRole('mufti')) {
                return redirect()->route('mufti.dashboard');
            }

            return redirect()->route('home');

        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['email' => 'Google login failed. Please try again.']);
        }
    }
}
