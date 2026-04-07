<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\QuestionAdminController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\FatwaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Mufti\MuftiController;
use App\Http\Controllers\QuestionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');

// Fatwa Archive
Route::get('/fatwa-archive', [FatwaController::class, 'index'])->name('fatwa.archive');
Route::get('/fatwa-archive/{fatwa}', [FatwaController::class, 'show'])->name('fatwa.show');

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/logout', [LoginController::class, 'logout']);


// OTP Verification
Route::get('/verify-otp', [\App\Http\Controllers\Auth\OtpVerificationController::class, 'showVerifyForm'])->name('otp.verify.form');
Route::post('/verify-otp', [\App\Http\Controllers\Auth\OtpVerificationController::class, 'verify'])->name('otp.verify.submit');

// OTP Login
Route::post('/login/otp/send', [\App\Http\Controllers\Auth\LoginController::class, 'sendLoginOtp'])->name('login.otp.send');

/*
|--------------------------------------------------------------------------
| Questioner Routes (auth)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/user/dashboard', [\App\Http\Controllers\User\UserDashboardController::class, 'index'])->name('user.dashboard');
    Route::get('/user/questions/{question}', [\App\Http\Controllers\User\UserDashboardController::class, 'show'])->name('user.question.show');
    Route::get('/ask-question', [\App\Http\Controllers\QuestionController::class, 'create'])->name('question.create');
    Route::post('/ask-question', [\App\Http\Controllers\QuestionController::class, 'store'])->name('question.store');
});



/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Questions
    Route::get('/questions', [QuestionAdminController::class, 'index'])->name('questions.index');
    Route::get('/questions/{question}', [QuestionAdminController::class, 'show'])->name('questions.show');
    Route::post('/questions/{question}/accept', [QuestionAdminController::class, 'accept'])->name('questions.accept');
    Route::post('/questions/{question}/assign', [QuestionAdminController::class, 'assign'])->name('questions.assign');

    // Admin write answer
    Route::get('/questions/{question}/answer', [QuestionAdminController::class, 'createAnswer'])->name('questions.answer');
    Route::post('/questions/{question}/answer', [QuestionAdminController::class, 'storeAnswer'])->name('questions.answer.store');

    // Verification
    Route::get('/questions/{question}/verify', [QuestionAdminController::class, 'verify'])->name('questions.verify');
    Route::post('/questions/{question}/approve', [QuestionAdminController::class, 'approve'])->name('questions.approve');
    Route::post('/questions/{question}/reject', [QuestionAdminController::class, 'reject'])->name('questions.reject');
    Route::post('/questions/{question}/correction', [QuestionAdminController::class, 'requestCorrection'])->name('questions.correction');
    Route::get('/questions/{question}/history', [QuestionAdminController::class, 'history'])->name('questions.history');

    // File download
    Route::get('/files/{file}/download', [QuestionAdminController::class, 'downloadFile'])->name('files.download');

    // Categories
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Mufti Management
    Route::get('/muftis', [\App\Http\Controllers\Admin\MuftiManagementController::class, 'index'])->name('muftis.index');
    Route::get('/muftis/create', [\App\Http\Controllers\Admin\MuftiManagementController::class, 'create'])->name('muftis.create');
    Route::post('/muftis', [\App\Http\Controllers\Admin\MuftiManagementController::class, 'store'])->name('muftis.store');
    Route::get('/muftis/{user}/edit', [\App\Http\Controllers\Admin\MuftiManagementController::class, 'edit'])->name('muftis.edit');
    Route::put('/muftis/{user}', [\App\Http\Controllers\Admin\MuftiManagementController::class, 'update'])->name('muftis.update');
    Route::delete('/muftis/{user}', [\App\Http\Controllers\Admin\MuftiManagementController::class, 'destroy'])->name('muftis.destroy');
    Route::patch('/muftis/{user}/toggle-status', [\App\Http\Controllers\Admin\MuftiManagementController::class, 'toggleStatus'])->name('muftis.toggle-status');

    // Questioner Management
    Route::get('/questioners', [\App\Http\Controllers\Admin\QuestionerController::class, 'index'])->name('questioners.index');
    Route::get('/questioners/{user}', [\App\Http\Controllers\Admin\QuestionerController::class, 'show'])->name('questioners.show');
});

/*
|--------------------------------------------------------------------------
| Mufti Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:mufti'])->prefix('mufti')->name('mufti.')->group(function () {
    Route::get('/dashboard', [MuftiController::class, 'dashboard'])->name('dashboard');
    Route::get('/questions', [MuftiController::class, 'questions'])->name('questions');
    Route::get('/questions/{question}', [MuftiController::class, 'showQuestion'])->name('questions.show');
    Route::post('/questions/{question}/answer', [MuftiController::class, 'submitAnswer'])->name('questions.answer');
    Route::get('/profile', [MuftiController::class, 'profile'])->name('profile');
    Route::put('/profile', [MuftiController::class, 'updateProfile'])->name('profile.update');
});

// Temporary Migration Route
Route::get('/run-migrations-live', function () {
    // This runs the migration command programmatically
    \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    return "Migrations executed successfully on the live server!";
});

