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
        $user = \App\Models\User::where($login_type, $request->login_id)->first();

        if ($user) {
            $avatarUrl = null;
            if ($user->avatar) {
                $avatarUrl = str_starts_with($user->avatar, 'http') ? $user->avatar : asset($user->avatar);
            }

            return response()->json([
                'exists'   => true,
                'name'     => $user->despl_name ?: $user->name,
                'email'    => $user->email,
                'avatar'   => $avatarUrl,
                'initial'  => strtoupper(mb_substr($user->name ?: 'U', 0, 1)),
                'role'     => ucfirst($user->role?->name ?? 'Member'),
            ]);
        }

        return response()->json(['exists' => false]);
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

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success'      => true,
                    'redirect_url' => $intendedUrl,
                    'message'      => 'Signed in successfully! Redirecting...',
                ]);
            }

            return redirect($intendedUrl);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'The password you entered is incorrect. Please try again.',
            ], 422);
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
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'The provided credentials do not match our records.',
                ], 404);
            }
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
            \Illuminate\Support\Facades\Log::warning('OTP Mail Error: ' . $e->getMessage());
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to send email OTP. Please check mail settings or sign in with your password.',
                ], 500);
            }
            return back()->withErrors(['login_id' => 'Error sending OTP. Please check your Mail configuration.']);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'email'   => $user->email,
                'message' => 'A 6-digit login OTP has been sent to ' . $user->email,
            ]);
        }

        return redirect()->route('otp.verify.form', ['email' => $user->email])
            ->with('success', 'An OTP has been sent to your email.');
    }

    public function verifyLoginOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'otp'   => ['required', 'string', 'size:6'],
        ]);

        $user = \App\Models\User::where('email', $request->email)->first();

        // Check if OTP matches and is not expired
        if ($user && (string)$user->otp === (string)$request->otp && $user->otp_expires_at > now()) {
            // Clear OTP
            $user->update([
                'otp'            => null,
                'otp_expires_at' => null,
                'is_active'      => true,
            ]);

            Auth::login($user, $request->boolean('remember', true));
            $request->session()->regenerate();

            $defaultUrl = $this->redirectBasedOnRoleUrl($user);
            $intendedUrl = session()->pull('url.intended', $defaultUrl);
            if (str_contains($intendedUrl, '/api/') || str_contains($intendedUrl, '.json')) {
                $intendedUrl = $defaultUrl;
            }

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success'      => true,
                    'redirect_url' => $intendedUrl,
                    'message'      => 'Code verified successfully! Redirecting...',
                ]);
            }

            return redirect($intendedUrl)->with('success', 'Logged in successfully!');
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired 6-digit OTP code. Please check and try again.',
            ], 422);
        }

        return back()->withErrors(['otp' => 'Invalid or expired OTP. Please try again.']);
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
