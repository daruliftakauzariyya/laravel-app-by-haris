<?php

namespace App\Http\Controllers\Api\Mufti;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\RegistrationOtpMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class MuftiAuthApiController extends Controller
{
    /**
     * STEP 1: Send OTP to Mufti Email
     */
    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'success' => false,
                'message' => 'Please enter a valid official email address.'
            ], 422);
        }

        $email = strtolower(trim($request->email));

        // 1. Locate Scholar User
        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json([
                'status'  => 'error',
                'success' => false,
                'message' => 'Scholar account with this email was not found in our records.'
            ], 404);
        }

        // 2. Strict Role Enforcement: Only Mufti Role
        $isMufti = $user->hasRole('mufti')
            || ($user->role?->name && strtolower($user->role->name) === 'mufti')
            || ($user->can_check_approve == 1);

        if (!$isMufti) {
            return response()->json([
                'status'  => 'error',
                'success' => false,
                'message' => 'Access restricted: Only verified Muftis can log in to the Scholar Portal.'
            ], 403);
        }

        // 3. Deactivated account check
        if (isset($user->is_active) && !$user->is_active) {
            return response()->json([
                'status'  => 'error',
                'success' => false,
                'message' => 'Your scholar account is currently deactivated. Please contact administration.'
            ], 403);
        }

        // 4. Generate 6-Digit Secure Numeric OTP
        $otp = (string)rand(100000, 999999);

        // Store OTP in database columns and cache for 15 minutes
        $user->update([
            'otp'            => $otp,
            'otp_expires_at' => now()->addMinutes(15),
        ]);
        Cache::put('mufti_otp_' . $email, $otp, now()->addMinutes(15));

        // 5. Send OTP via Email
        try {
            if (class_exists(RegistrationOtpMail::class)) {
                Mail::to($user->email)->send(new RegistrationOtpMail($otp));
            } else {
                Mail::raw("Assalamu Alaikum wa Rahmatullah {$user->name},\n\nYour 6-digit verification code to sign in to the Darul Ifta Kauzariyya Scholar Portal is:\n\n{$otp}\n\nThis code will expire in 15 minutes.\n\nDarul Ifta Kauzariyya Security Team", function ($message) use ($user, $otp) {
                    $message->to($user->email)
                            ->subject("Darul Ifta Scholar Portal - OTP Code: {$otp}");
                });
            }
        } catch (\Exception $e) {
            Log::error('Mufti OTP Mail dispatch error: ' . $e->getMessage());
            try {
                Mail::raw("Assalamu Alaikum wa Rahmatullah {$user->name},\n\nYour verification code is: {$otp}", function ($msg) use ($user, $otp) {
                    $msg->to($user->email)->subject("Verification OTP: {$otp}");
                });
            } catch (\Exception $ex) {
                Log::error('Raw mail fallback failed: ' . $ex->getMessage());
            }
        }

        // 6. Return response with scholar profile preview for Step 2 UI
        return response()->json([
            'status'  => 'success',
            'success' => true,
            'message' => 'A 6-digit verification code has been dispatched to your official email.',
            'scholar' => [
                'id'                 => $user->id,
                'name'               => $user->name,
                'email'              => $user->email,
                'mobile'             => $user->mobile,
                'role'               => 'mufti',
                'can_check_approve'  => (bool)($user->can_check_approve ?? false),
                'avatar'             => $user->avatar ? asset($user->avatar) : null,
                'country'            => $user->country ?? 'Darul Ifta Academic Faculty',
                'is_active'          => (bool)($user->is_active ?? true),
            ]
        ]);
    }

    /**
     * STEP 2 & 3: Verify OTP & Issue Token
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp'   => 'required|string|min:4|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'success' => false,
                'message' => 'Please provide a valid email and 6-digit OTP code.'
            ], 422);
        }

        $email = strtolower(trim($request->email));
        $submittedOtp = trim($request->otp);

        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json([
                'status'  => 'error',
                'success' => false,
                'message' => 'Scholar account not found.'
            ], 404);
        }

        // Check OTP validity against DB and Cache
        $cachedOtp = Cache::get('mufti_otp_' . $email);
        $dbOtpMatch = ($user->otp == $submittedOtp && $user->otp_expires_at && $user->otp_expires_at > now());
        $cacheOtpMatch = ($cachedOtp && (string)$cachedOtp === (string)$submittedOtp);

        if (!$dbOtpMatch && !$cacheOtpMatch) {
            return response()->json([
                'status'  => 'error',
                'success' => false,
                'message' => 'Invalid or expired verification code. Please check and try again.'
            ], 401);
        }

        // Clear used OTP
        $user->update([
            'otp'            => null,
            'otp_expires_at' => null,
        ]);
        Cache::forget('mufti_otp_' . $email);

        if (method_exists($user, 'recordLogin')) {
            $user->recordLogin();
        }

        // Generate Sanctum Bearer Token
        $token = $user->createToken('mufti-mobile-token')->plainTextToken;

        return response()->json([
            'status'  => 'success',
            'success' => true,
            'message' => 'Authentication successful. Welcome to the Scholar Command Center.',
            'token'   => $token,
            'user'    => [
                'id'                 => $user->id,
                'name'               => $user->name,
                'email'              => $user->email,
                'mobile'             => $user->mobile,
                'role'               => 'mufti',
                'can_check_approve'  => (bool)($user->can_check_approve ?? false),
                'avatar'             => $user->avatar ? asset($user->avatar) : null,
                'country'            => $user->country ?? 'Darul Ifta Academic Faculty',
                'is_active'          => (bool)($user->is_active ?? true),
            ]
        ]);
    }

    /**
     * Legacy Password Login (Backward Compatibility)
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid official scholar credentials provided.'
            ], 401);
        }

        if (isset($user->is_active) && !$user->is_active) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Your scholar account is currently deactivated. Please contact administration.'
            ], 403);
        }

        $token = $user->createToken('mufti-mobile-token')->plainTextToken;

        return response()->json([
            'status'  => 'success',
            'message' => 'Welcome to the Scholar Command Center',
            'token'   => $token,
            'user'    => [
                'id'                 => $user->id,
                'name'               => $user->name,
                'email'              => $user->email,
                'mobile'             => $user->mobile,
                'role'               => $user->role ?? 'mufti',
                'can_check_approve'  => (bool)($user->can_check_approve ?? false),
                'avatar'             => $user->avatar ? asset($user->avatar) : null,
                'country'            => $user->country ?? 'Global Scholar',
                'is_active'          => (bool)($user->is_active ?? true),
            ]
        ]);
    }

    /**
     * Scholar Session Status & Token Verification
     */
    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'status' => 'success',
            'data'   => [
                'id'                 => $user->id,
                'name'               => $user->name,
                'email'              => $user->email,
                'mobile'             => $user->mobile,
                'role'               => $user->role ?? 'mufti',
                'can_check_approve'  => (bool)($user->can_check_approve ?? false),
                'avatar'             => $user->avatar ? asset($user->avatar) : null,
                'country'            => $user->country ?? 'Global Scholar',
                'is_active'          => (bool)($user->is_active ?? true),
            ]
        ]);
    }

    /**
     * Scholar Logout
     */
    public function logout(Request $request)
    {
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Successfully logged out from Scholar Portal.'
        ]);
    }
}
