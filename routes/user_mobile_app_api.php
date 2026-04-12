<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Mail\RegistrationOtpMail;

/*
|--------------------------------------------------------------------------
| User Mobile App Routes
|--------------------------------------------------------------------------
*/

// --- PUBLIC ROUTES ---

// List fatwas
Route::get('/fatwas', function (Request $request) {
    $query = \App\Models\Fatwa::with(['question.category', 'question.madhab', 'muftiAnswer.mufti', 'muftiAnswer.supportingReferences'])
                ->latest('published_at');

    if ($request->filled('search')) {
        $term = $request->search;
        $query->where(function ($q) use ($term) {
            $q->whereHas('question', function ($sq) use ($term) {
                $sq->where('body', 'like', "%{$term}%")
                   ->orWhere('title', 'like', "%{$term}%");
            })
            ->orWhereHas('muftiAnswer', function ($aq) use ($term) {
                $aq->where('full_answer', 'like', "%{$term}%")
                   ->orWhere('summary', 'like', "%{$term}%");
            });
        });
    }

    $fatwas = $query->paginate(20);
    return response()->json(['status' => 'success', 'data' => $fatwas]);
});

// Single fatwa
Route::get('/fatwas/{fatwa}', function (\App\Models\Fatwa $fatwa) {
    $fatwa->load(['question.category', 'question.madhab', 'muftiAnswer.mufti', 'muftiAnswer.supportingReferences', 'publisher']);
    return response()->json(['status' => 'success', 'data' => $fatwa]);
});

// Categories
Route::get('/categories', function () {
    $categories = \App\Models\Category::with('children')
                ->whereNull('parent_id')
                ->orderBy('sort_order')
                ->get();
    return response()->json(['status' => 'success', 'data' => $categories]);
});

// Madhabs
Route::get('/madhabs', function () {
    $madhabs = \App\Models\Madhab::all();
    return response()->json(['status' => 'success', 'data' => $madhabs]);
});


// --- AUTHENTICATION ROUTES ---

// Standard Password Login
Route::post('/auth/login', function (Request $request) {
    $request->validate(['email' => 'required|email', 'password' => 'required']);
    $user = User::where('email', $request->email)->first();
    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['status' => 'error', 'message' => 'Invalid credentials'], 401);
    }
    $token = $user->createToken('api-token')->plainTextToken;
    return response()->json(['status' => 'success', 'token' => $token, 'user' => $user]);
});

// Send OTP for Login
Route::post('/auth/otp/send', function (Request $request) {
    $request->validate(['email' => 'required|email|exists:users,email']);
    $user = User::where('email', $request->email)->first();
    
    $otp = rand(100000, 999999);
    $user->update([
        'otp' => $otp,
        'otp_expires_at' => now()->addMinutes(15),
    ]);

    try {
        Mail::to($user->email)->send(new RegistrationOtpMail($otp));
    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'message' => 'Failed to send OTP email'], 500);
    }

    return response()->json(['status' => 'success', 'message' => 'OTP sent successfully']);
});

// Verify OTP Login
Route::post('/auth/otp/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email|exists:users,email',
        'otp' => 'required|numeric'
    ]);

    $user = User::where('email', $request->email)->first();

    if ($user->otp == $request->otp && $user->otp_expires_at > now()) {
        $user->update(['otp' => null, 'otp_expires_at' => null]);
        $token = $user->createToken('api-token')->plainTextToken;
        return response()->json(['status' => 'success', 'token' => $token, 'user' => $user]);
    }

    return response()->json(['status' => 'error', 'message' => 'Invalid or expired OTP'], 401);
});

// 3-Step Registration
Route::post('/auth/register', function (Request $request) {
    $request->validate([
        'name'      => 'required|string|max:255',
        'email'     => 'required|email|unique:users',
        'mobile'    => 'required|string',
        'password'  => 'required|min:8',
        'madhab_id' => 'nullable|exists:madhabs,id',
    ]);

    $user = User::create([
        'name'      => $request->name,
        'email'     => $request->email,
        'mobile'    => $request->mobile,
        'password'  => Hash::make($request->password),
        'madhab_id' => $request->madhab_id,
        'profile_completed' => true,
    ]);

    $user->assignRole('questioner');
    $token = $user->createToken('api-token')->plainTextToken;
    return response()->json(['status' => 'success', 'token' => $token, 'user' => $user], 201);
});


// --- PROTECTED ROUTES ---

Route::middleware('auth:sanctum')->group(function () {
    
    // Submit inquiry with file upload support
    Route::post('/questions', function (Request $request) {
        $validated = $request->validate([
            'title'       => 'nullable|string|max:255',
            'body'        => 'required|min:20',
            'category_id' => 'required|exists:categories,id',
            'madhab_id'   => 'nullable|exists:madhabs,id',
            'files.*'     => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB limit
        ]);

        $question = \App\Models\Question::create([
            'question_id' => \App\Models\Question::generateQuestionId(),
            'user_id'     => $request->user()->id,
            'category_id' => $request->category_id,
            'madhab_id'   => $request->madhab_id,
            'title'       => $request->title,
            'body'        => $request->body,
            'status'      => 'pending',
        ]);

        // Handle file uploads
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('questions', 'public');
                \App\Models\QuestionFile::create([
                    'question_id'   => $question->id,
                    'original_name' => $file->getClientOriginalName(),
                    'file_path'     => $path,
                    'file_type'     => $file->getClientMimeType(),
                    'file_size'     => $file->getSize(),
                ]);
            }
        }

        return response()->json(['status' => 'success', 'question_id' => $question->question_id], 201);
    });

    // List user's inquiries
    Route::get('/questions', function (Request $request) {
        $questions = \App\Models\Question::where('user_id', $request->user()->id)
                    ->with(['category', 'madhab', 'fatwa'])
                    ->latest()
                    ->paginate(20);
        return response()->json(['status' => 'success', 'data' => $questions]);
    });

    // User Profile
    Route::get('/profile', function (Request $request) {
        return response()->json(['status' => 'success', 'data' => $request->user()]);
    });
});
