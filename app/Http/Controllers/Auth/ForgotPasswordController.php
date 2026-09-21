<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\RegistrationOtpMail;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetOtp(Request $request)
    {
        $request->validate([
            'login_id' => 'required|string',
        ]);

        $login_type = filter_var($request->login_id, FILTER_VALIDATE_EMAIL) ? 'email' : 'mobile';
        $user = User::where($login_type, $request->login_id)->first();

        if (!$user) {
            return back()->withErrors(['login_id' => 'We could not find a user with that identifier.']);
        }

        $otp = rand(100000, 999999);
        $user->update([
            'otp' => $otp,
            'otp_expires_at' => now()->addMinutes(15),
        ]);

        try {
            // Reusing the registration OTP mail for simplicity
            Mail::to($user->email)->send(new RegistrationOtpMail($otp));
        } catch (\Exception $e) {
            return back()->withErrors(['login_id' => 'Error sending OTP. Please check your Mail configuration.']);
        }

        return redirect()->route('password.reset', ['email' => $user->email])
            ->with('success', 'A password reset OTP has been sent to your email.');
    }

    public function showResetForm($email)
    {
        return view('auth.reset-password', ['email' => $email]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|numeric',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || $user->otp != $request->otp || now()->isAfter($user->otp_expires_at)) {
            return back()->withErrors(['otp' => 'The OTP is invalid or has expired.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
            'otp' => null,
            'otp_expires_at' => null,
        ]);

        $rolePrefix = ($user->hasRole('mufti') || $user->hasRole('admin')) ? 'Mufti ' . $user->name : ($user->name ?? 'User');
        \App\Models\FatwaActivityLog::log(
            null,
            'password_reset',
            $rolePrefix . ' Reset Password',
            'bi-shield-lock-fill',
            'warning',
            $rolePrefix . ' reset password successfully via OTP @ ' . now()->format('h:i A : d-m-Y'),
            $user->id
        );

        return redirect()->route('login')->with('success', 'Your password has been reset successfully. You can now login.');
    }
}
