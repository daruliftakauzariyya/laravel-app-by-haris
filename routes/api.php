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

// Public: Get single fatwa (optimized with ID lookup to bypass model binding DB queries)
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

// Auth: Login
Route::post('/auth/login', function (Request $request) {
    $request->validate(['email' => 'required|email', 'password' => 'required']);
    $user = User::where('email', $request->email)->first();
    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['status' => 'error', 'message' => 'Invalid credentials'], 401);
    }
    $token = $user->createToken('api-token')->plainTextToken;
    return response()->json(['status' => 'success', 'token' => $token, 'user' => $user]);
});

// Auth: Register
Route::post('/auth/register', function (Request $request) {
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:8',
    ]);
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
    ]);
    $user->assignRole('questioner');
    $token = $user->createToken('api-token')->plainTextToken;
    return response()->json(['status' => 'success', 'token' => $token, 'user' => $user], 201);
});

// Protected API routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn(Request $request) => $request->user());

    // Submit question via API
    Route::post('/questions', function (Request $request) {
        $request->validate([
            'body' => 'required|min:20',
            'madhab' => 'required|in:Hanafi,Shafii,Maliki,Hanbali,Other',
        ]);
        $question = \App\Models\Question::create([
            'question_id' => \App\Models\Question::generateQuestionId(),
            'user_id' => $request->user()->id,
            'body' => $request->body,
            'title' => $request->title,
            'madhab' => $request->madhab,
            'status' => 'pending',
        ]);
        return response()->json(['status' => 'success', 'question_id' => $question->question_id], 201);
    });

    // List my questions
    Route::get('/questions', function (Request $request) {
        $questions = \App\Models\Question::where('user_id', $request->user()->id)->latest()->paginate(20);
        return response()->json(['status' => 'success', 'data' => $questions]);
    });

    // Admin (Management) Routes
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/pending-call', [\App\Http\Controllers\Api\AdminApiController::class, 'pendingCall']);
        Route::get('/dashboard-stats', [\App\Http\Controllers\Api\AdminApiController::class, 'dashboardStats']);
        Route::post('/questions/{question}/accept', [\App\Http\Controllers\Api\AdminApiController::class, 'acceptQuestion']);
    });

    // Scholar (Mufti) Routes
    Route::middleware('role:mufti')->prefix('mufti')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Api\MuftiApiController::class, 'dashboard']);
        Route::get('/questions', [\App\Http\Controllers\Api\MuftiApiController::class, 'questions']);
        Route::get('/questions/{question}', [\App\Http\Controllers\Api\MuftiApiController::class, 'showQuestion']);
        Route::post('/questions/{question}/answer', [\App\Http\Controllers\Api\MuftiApiController::class, 'submitAnswer']);
        Route::get('/profile', [\App\Http\Controllers\Api\MuftiApiController::class, 'profile']);
    });
});
