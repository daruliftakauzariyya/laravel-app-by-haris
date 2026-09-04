<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\FatwaController;
use App\Http\Controllers\QuestionController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Darul Ifta Portal
|--------------------------------------------------------------------------
*/

// Include Dedicated Mufti Mobile App API Routes
require __DIR__ . '/mufti_mobile_app_api.php';

// Public: List published fatwas with optional search
Route::get('/fatwas', function (Request $request) {
    $page = $request->get('page', 1);
    $search = $request->get('search', '');
    $cacheKey = "api_fatwas_page_{$page}_search_" . md5($search);

    $fatwas = \Illuminate\Support\Facades\Cache::remember($cacheKey, 600, function () use ($search) {
        $query = \App\Models\Fatwa::with(['question.category', 'question.madhab', 'muftiAnswer.mufti', 'muftiAnswer.supportingReferences'])
            ->latest('published_at');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('question', function ($sq) use ($search) {
                    $sq->where('body', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%");
                })
                    ->orWhereHas('muftiAnswer', function ($aq) use ($search) {
                        $aq->where('full_answer', 'like', "%{$search}%")
                            ->orWhere('summary', 'like', "%{$search}%");
                    });
            });
        }

        return $query->paginate(20);
    });

    return response()->json([
        'status' => 'success',
        'data' => $fatwas,
    ]);
});

// Public: Get categories tree
Route::get('/categories', function () {
    $categories = \Illuminate\Support\Facades\Cache::remember('api_categories', 3600, function () {
        return \App\Models\Category::with('children')
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();
    });
    return response()->json(['status' => 'success', 'data' => $categories]);
});

// Public: Get single fatwa
Route::get('/fatwas/{fatwaId}', function ($fatwaId) {
    $fatwa = \Illuminate\Support\Facades\Cache::remember("api_fatwa_{$fatwaId}", 3600, function () use ($fatwaId) {
        $item = \App\Models\Fatwa::find($fatwaId);
        if ($item) {
            $item->load(['question.category', 'question.madhab', 'muftiAnswer.mufti', 'muftiAnswer.supportingReferences', 'publisher']);
        }
        return $item;
    });

    if (!$fatwa) {
        return response()->json(['status' => 'error', 'message' => 'Fatwa not found'], 404);
    }

    return response()->json(['status' => 'success', 'data' => $fatwa]);
});

// Auth: Public Login
Route::post('/auth/login', function (Request $request) {
    $request->validate(['email' => 'required|email', 'password' => 'required']);
    $user = User::where('email', $request->email)->first();
    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['status' => 'error', 'message' => 'Invalid credentials'], 401);
    }
    $token = $user->createToken('api-token')->plainTextToken;
    return response()->json([
        'status' => 'success', 
        'token'  => $token, 
        'user'   => [
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
});

// Protected API routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn(Request $request) => $request->user());
});
