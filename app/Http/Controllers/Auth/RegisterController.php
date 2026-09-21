<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function showRegistrationForm(Request $request)
    {
        $loginId = trim((string) $request->query('login_id', ''));
        $email = trim((string) $request->query('email', ''));
        $mobile = trim((string) $request->query('mobile', ''));

        // Only accessible via redirection with email/mobile/login_id
        if (empty($loginId) && empty($email) && empty($mobile)) {
            return redirect()->route('login')->with('warning', 'Please enter your email or mobile on the Join screen to get started.');
        }

        if (empty($loginId)) {
            $loginId = $email ?: $mobile;
        }

        // Force the destination to ask-question if not already set, to fulfill the "auto redirect" requirement
        if (!session()->has('url.intended')) {
            session(['url.intended' => route('question.create')]);
        }

        $madhabs = \App\Models\Madhab::all();
        return view('auth.register', compact('loginId', 'email', 'mobile', 'madhabs'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'country_code'   => ['required', 'string', 'max:10'],
            'mobile'         => ['required', 'string', 'max:20'],
            'email'          => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'       => ['required', Password::defaults()],
        ]);

        $otp = rand(100000, 999999);

        $user = User::create([
            'name'           => $request->name,
            'despl_name'     => $request->name,
            'country_code'   => $request->country_code,
            'mobile'         => $request->mobile,
            'email'          => $request->email,
            'password'       => Hash::make($request->password),
            'otp'            => $otp,
            'otp_expires_at' => now()->addMinutes(15),
            'is_active'      => false,
            'profile_completed' => false,
        ]);

        try {
            $user->assignRole('user'); // Or questioner, depending on your system
        } catch (\Exception $e) {
            // Suppress role error if role doesn't exist
        }

        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\RegistrationOtpMail($otp));
        } catch (\Exception $e) {
            // Handle mail error gracefully in UI
            return redirect()->route('otp.verify.form', ['email' => $user->email])
                ->with('error', 'Registration successful, but there was an error sending the OTP email. Please check your Mail configuration.');
        }

        return redirect()->route('otp.verify.form', ['email' => $user->email])
            ->with('success', 'Registration successful! An OTP has been sent to your email.');
    }
}
