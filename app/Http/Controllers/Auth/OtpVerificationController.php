<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OtpVerificationController extends Controller
{
    public function showVerifyForm(Request $request)
    {
        $email = $request->query('email');
        if (!$email) {
            return redirect()->route('login');
        }
        return view('auth.verify-otp', compact('email'));
    }

    public function verify(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|numeric'
        ]);

        $user = User::where('email', $request->email)->first();

        // Check if OTP matches and is not expired
        if ($user->otp === $request->otp && $user->otp_expires_at > now()) {
            
            // Clear OTP and activate user
            $user->update([
                'otp' => null,
                'otp_expires_at' => null,
                'is_active' => true,
                'profile_completed' => true
            ]);

            Auth::login($user);

            // Redirect based on role, respecting intended URLs
            if ($user->hasRole('admin')) {
                return redirect()->intended(route('admin.dashboard'))->with('success', 'Account verified successfully!');
            }
            if ($user->hasRole('mufti')) {
                return redirect()->intended(route('mufti.dashboard'))->with('success', 'Account verified successfully!');
            }
            return redirect()->intended(route('user.dashboard'))->with('success', 'Account verified successfully!');
        }

        return back()->withErrors(['otp' => 'Invalid or expired OTP. Please try again.']);
    }
}
