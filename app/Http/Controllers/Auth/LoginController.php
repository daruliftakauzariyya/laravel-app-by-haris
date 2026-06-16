<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function checkAccount(Request $request)
    {
        $request->validate([
            'login_id' => ['required', 'string'],
        ]);

        $login_type = filter_var($request->login_id, FILTER_VALIDATE_EMAIL) ? 'email' : 'mobile';
        $exists = \App\Models\User::where($login_type, $request->login_id)->exists();

        return response()->json(['exists' => $exists]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'login_id' => ['required', 'string'],
            'password' => ['required'],
        ]);

        $login_type = filter_var($request->login_id, FILTER_VALIDATE_EMAIL) ? 'email' : 'mobile';

        if (Auth::attempt([$login_type => $request->login_id, 'password' => $request->password], $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            $defaultUrl = $this->redirectBasedOnRoleUrl($user);

            // Prevent redirecting to API/JSON endpoints after login
            $intendedUrl = session()->pull('url.intended', $defaultUrl);
            if (str_contains($intendedUrl, '/api/') || str_contains($intendedUrl, '.json')) {
                $intendedUrl = $defaultUrl;
            }

            return redirect($intendedUrl);
        }

        return back()->withErrors([
            'login_id' => 'The provided credentials do not match our records.',
        ])->onlyInput('login_id');
    }

    public function sendLoginOtp(Request $request)
    {
        $request->validate([
            'login_id' => ['required', 'string'],
        ]);

        $login_type = filter_var($request->login_id, FILTER_VALIDATE_EMAIL) ? 'email' : 'mobile';
        
        $user = \App\Models\User::where($login_type, $request->login_id)->first();
        if (!$user) {
            return back()->withErrors(['login_id' => 'The provided credentials do not match our records.'])->onlyInput('login_id');
        }

        $otp = rand(100000, 999999);
        $user->update([
            'otp' => $otp,
            'otp_expires_at' => now()->addMinutes(15),
        ]);

        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\RegistrationOtpMail($otp));
        } catch (\Exception $e) {
            return back()->withErrors(['login_id' => 'Error sending OTP. Please check your Mail configuration.']);
        }

        return redirect()->route('otp.verify.form', ['email' => $user->email])
            ->with('success', 'An OTP has been sent to your email.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    protected function redirectBasedOnRoleUrl($user)
    {
        if ($user->hasRole('admin')) {
            return route('admin.dashboard');
        }
        if ($user->hasRole('mufti')) {
            return route('mufti.dashboard');
        }
        return route('user.dashboard');
    }
}
