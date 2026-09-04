<?php

namespace App\Http\Controllers\Api\Mufti;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MuftiAuthApiController extends Controller
{
    /**
     * Scholar Login for Mobile App
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
