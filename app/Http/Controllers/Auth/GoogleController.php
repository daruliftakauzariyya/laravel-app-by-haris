<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    /**
     * Handle Google Identity Services (GSI) / One-Tap & Popup Token.
     */
    public function handleToken(Request $request)
    {
        $accessToken = $request->input('access_token');
        $idToken = $request->input('id_token') ?: $request->input('credential');

        if (!$accessToken && !$idToken) {
            return response()->json(['success' => false, 'message' => 'No credential received from Google.'], 400);
        }

        $googleUser = null;

        try {
            if ($accessToken) {
                $response = Http::timeout(10)->withToken($accessToken)
                    ->get('https://www.googleapis.com/oauth2/v3/userinfo');
                if ($response->successful()) {
                    $googleUser = $response->json();
                }
            } elseif ($idToken) {
                $response = Http::timeout(10)->get('https://oauth2.googleapis.com/tokeninfo', [
                    'id_token' => $idToken,
                ]);
                if ($response->successful()) {
                    $googleUser = $response->json();
                }
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error connecting to Google: ' . $e->getMessage()], 500);
        }

        if (!$googleUser || empty($googleUser['sub']) || empty($googleUser['email'])) {
            return response()->json(['success' => false, 'message' => 'Failed to verify Google account details.'], 401);
        }

        $googleId = $googleUser['sub'];
        $googleEmail = $googleUser['email'];
        $googleAvatar = $googleUser['picture'] ?? null;
        $googleName = $googleUser['name'] ?? ($googleUser['given_name'] ?? 'User');

        $isLinkingAction = session()->pull('google_oauth_action') === 'link_account' || $request->input('action') === 'link';

        // 1. Logged-in user linking account
        if (Auth::check() || $isLinkingAction) {
            $currentUser = Auth::user();
            if (!$currentUser) {
                return response()->json(['success' => false, 'message' => 'Session expired. Please sign in before connecting Google.'], 401);
            }

            $conflictUser = User::where('google_id', $googleId)
                ->where('id', '!=', $currentUser->id)
                ->first();

            if ($conflictUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'This Google account is already linked to another user (' . $conflictUser->email . ').'
                ], 422);
            }

            $currentUser->update([
                'google_id' => $googleId,
                'avatar'    => $currentUser->avatar ?: $googleAvatar,
            ]);

            session()->flash('success', 'Google account (' . $googleEmail . ') connected successfully!');

            return response()->json([
                'success'  => true,
                'message'  => 'Google account connected successfully!',
                'redirect' => route('user.settings')
            ]);
        }

        // 2. Public sign-in / registration flow
        $user = User::where('google_id', $googleId)->first();

        if (!$user && $googleEmail) {
            $user = User::where('email', $googleEmail)->first();
            if ($user) {
                $user->update([
                    'google_id' => $googleId,
                    'avatar'    => $user->avatar ?: $googleAvatar,
                ]);
            }
        }

        if (!$user) {
            $role = Role::whereIn('name', ['questioner', 'user'])->first();
            $roleId = $role ? $role->id : 3;

            $user = User::create([
                'role_id'           => $roleId,
                'name'              => $googleName,
                'despl_name'        => $googleName,
                'email'             => $googleEmail,
                'google_id'         => $googleId,
                'avatar'            => $googleAvatar,
                'email_verified_at' => now(),
                'profile_completed' => false,
                'is_active'         => true,
            ]);
        }

        Auth::login($user, true);

        if ($user->hasRole('mufti') || $user->hasRole('admin')) {
            \App\Models\FatwaActivityLog::log(
                null,
                'google_login',
                'Mufti ' . $user->name . ' Logged in using Google Login',
                'bi-google',
                'danger',
                'Mufti ' . $user->name . ' logged in using Google login @ ' . now()->format('h:i A : d-m-Y'),
                $user->id
            );
        }

        $redirectUrl = route('user.dashboard');
        if ($user->hasRole('admin')) {
            $redirectUrl = route('admin.dashboard');
        } elseif ($user->hasRole('mufti')) {
            $redirectUrl = route('mufti.dashboard');
        }

        return response()->json([
            'success'  => true,
            'redirect' => session()->pull('url.intended', $redirectUrl),
            'message'  => 'Signed in successfully with Google!'
        ]);
    }

    /**
     * Redirect to Google OAuth consent screen (Public Login).
     */
    public function redirect()
    {
        $driver = Socialite::driver('google');
        $driver->setScopes([
            'https://www.googleapis.com/auth/userinfo.email',
            'https://www.googleapis.com/auth/userinfo.profile'
        ]);
        return $driver->redirect();
    }

    /**
     * Start OAuth flow to link Google account to currently logged-in user.
     */
    public function link()
    {
        session(['google_oauth_action' => 'link_account']);
        $driver = Socialite::driver('google');
        $driver->setScopes([
            'https://www.googleapis.com/auth/userinfo.email',
            'https://www.googleapis.com/auth/userinfo.profile'
        ]);
        return $driver->redirect();
    }

    /**
     * Handle OAuth callback from Google.
     */
    public function callback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['email' => 'Google sign-in was cancelled or failed. Please try again.']);
        }

        $googleId = $googleUser->getId();
        $googleEmail = $googleUser->getEmail();
        $googleAvatar = $googleUser->getAvatar();
        $googleName = $googleUser->getName() ?: 'User';
        $isLinkingAction = session()->pull('google_oauth_action') === 'link_account';

        // 1. Logged-in user linking their Google account from Settings
        if (Auth::check() || $isLinkingAction) {
            $currentUser = Auth::user();
            if (!$currentUser) {
                return redirect()->route('login')->withErrors(['email' => 'Session expired. Please sign in before connecting Google.']);
            }

            // Check if this Google ID is already linked to another account
            $conflictUser = User::where('google_id', $googleId)
                ->where('id', '!=', $currentUser->id)
                ->first();

            if ($conflictUser) {
                return redirect()->route('user.settings')->withErrors([
                    'google' => 'This Google account is already linked to another user (' . $conflictUser->email . ').'
                ]);
            }

            $currentUser->update([
                'google_id' => $googleId,
                'avatar'    => $currentUser->avatar ?: $googleAvatar,
            ]);

            return redirect()->route('user.settings')->with('success', 'Google account (' . $googleEmail . ') connected successfully!');
        }

        // 2. Public sign-in / registration flow (Guest)
        $user = User::where('google_id', $googleId)->first();

        if (!$user && $googleEmail) {
            $user = User::where('email', $googleEmail)->first();
            if ($user) {
                // Link Google account to this existing user
                $user->update([
                    'google_id' => $googleId,
                    'avatar'    => $user->avatar ?: $googleAvatar,
                ]);
            }
        }

        if (!$user) {
            // Find appropriate role_id for Questioner
            $role = Role::whereIn('name', ['questioner', 'user'])->first();
            $roleId = $role ? $role->id : 3;

            $user = User::create([
                'role_id'           => $roleId,
                'name'              => $googleName,
                'despl_name'        => $googleName,
                'email'             => $googleEmail,
                'google_id'         => $googleId,
                'avatar'            => $googleAvatar,
                'email_verified_at' => now(),
                'profile_completed' => false,
                'is_active'         => true,
            ]);
        }

        Auth::login($user, true);

        if ($user->hasRole('mufti') || $user->hasRole('admin')) {
            \App\Models\FatwaActivityLog::log(
                null,
                'google_login',
                'Mufti ' . $user->name . ' Logged in using Google Login',
                'bi-google',
                'danger',
                'Mufti ' . $user->name . ' logged in using Google login @ ' . now()->format('h:i A : d-m-Y'),
                $user->id
            );
        }

        // Role-based redirection
        if ($user->hasRole('admin')) {
            return redirect()->intended(route('admin.dashboard'));
        }
        if ($user->hasRole('mufti')) {
            return redirect()->intended(route('mufti.dashboard'));
        }

        return redirect()->intended(route('user.dashboard'));
    }

    /**
     * Unlink Google account from current user.
     */
    public function unlink(Request $request)
    {
        $user = Auth::user();

        if (!$user->google_id) {
            return back()->with('info', 'No Google account is currently connected.');
        }

        // Safety check: Ensure the user has an alternate way to sign in
        if (empty($user->password) && empty($user->email)) {
            return back()->withErrors(['google' => 'Cannot disconnect Google without an alternative login method.']);
        }

        $user->update([
            'google_id' => null,
        ]);

        return redirect()->route('user.settings')->with('success', 'Google account disconnected successfully.');
    }
}
