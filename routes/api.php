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

// Public: List published fatwas
Route::get('/fatwas', function (Request $request) {
    $fatwas = \App\Models\Fatwa::with(['question', 'muftiAnswer.mufti', 'muftiAnswer.supportingReferences'])
                ->latest('published_at')
                ->paginate(20);
    return response()->json([
        'status' => 'success',
        'data'   => $fatwas,
    ]);
});

// Public: Get categories tree
Route::get('/categories', function () {
    $categories = \App\Models\Category::with('children')
                ->whereNull('parent_id')
                ->orderBy('sort_order')
                ->get();
    return response()->json(['status' => 'success', 'data' => $categories]);
});

// Public: Get single fatwa
Route::get('/fatwas/{fatwa}', function (\App\Models\Fatwa $fatwa) {
    $fatwa->load(['question', 'muftiAnswer.mufti', 'muftiAnswer.supportingReferences', 'publisher']);
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
        'name'     => 'required|string|max:255',
        'email'    => 'required|email|unique:users',
        'password' => 'required|min:8',
    ]);
    $user = User::create([
        'name'     => $request->name,
        'email'    => $request->email,
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
            'body'   => 'required|min:20',
            'madhab' => 'required|in:Hanafi,Shafii,Maliki,Hanbali,Other',
        ]);
        $question = \App\Models\Question::create([
            'question_id' => \App\Models\Question::generateQuestionId(),
            'user_id'     => $request->user()->id,
            'body'        => $request->body,
            'title'       => $request->title,
            'madhab'      => $request->madhab,
            'status'      => 'pending',
        ]);
        return response()->json(['status' => 'success', 'question_id' => $question->question_id], 201);
    });

    // List my questions
    Route::get('/questions', function (Request $request) {
        $questions = \App\Models\Question::where('user_id', $request->user()->id)->latest()->paginate(20);
        return response()->json(['status' => 'success', 'data' => $questions]);
    });
});
