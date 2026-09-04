<?php

namespace App\Http\Controllers\Api\Mufti;

use App\Http\Controllers\Controller;
use App\Models\QuestionAndAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class MuftiProfileApiController extends Controller
{
    /**
     * Scholar Profile Summary
     */
    public function show(Request $request)
    {
        $user = Auth::user();

        $totalAnswers = QuestionAndAnswer::where('assigned_mufti_id', $user->id)
            ->whereIn('status', ['answered', 'approved', 'published'])
            ->count();

        $totalVerified = QuestionAndAnswer::where('checked_by_mufti_id', $user->id)
            ->count();

        return response()->json([
            'status' => 'success',
            'data'   => [
                'id'                 => $user->id,
                'name'               => $user->name,
                'email'              => $user->email,
                'mobile'             => $user->mobile,
                'country'            => $user->country ?? 'Global Scholar',
                'avatar'             => $user->avatar ? asset($user->avatar) : null,
                'can_check_approve'  => (bool)($user->can_check_approve ?? false),
                'is_active'          => (bool)($user->is_active ?? true),
                'role'               => $user->role ?? 'mufti',
                'stats'              => [
                    'total_answers'  => $totalAnswers,
                    'total_verified' => $totalVerified,
                ]
            ]
        ]);
    }

    /**
     * Update Profile Info / Password
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'mobile'           => 'nullable|string|max:30',
            'country'          => 'nullable|string|max:100',
            'current_password' => 'nullable|required_with:new_password',
            'new_password'     => 'nullable|min:8',
        ]);

        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Current password does not match records.'
                ], 422);
            }
            $user->password = Hash::make($request->new_password);
        }

        if ($request->has('mobile')) {
            $user->mobile = $request->mobile;
        }
        if ($request->has('country')) {
            $user->country = $request->country;
        }

        $user->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Profile details updated successfully.',
            'data'    => $user,
        ]);
    }
}
