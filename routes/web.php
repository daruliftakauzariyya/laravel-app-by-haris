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


// Google OAuth
Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('auth.google.callback');

// Profile Completion
Route::middleware('auth')->group(function () {
    Route::get('/profile/complete', [ProfileController::class, 'showCompleteForm'])->name('profile.complete');
    Route::post('/profile/complete', [ProfileController::class, 'complete'])->name('profile.complete.store');
});

/*
|--------------------------------------------------------------------------
| Questioner Routes (auth + profile completed)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'profile.completed'])->group(function () {
    Route::get('/ask-question', [QuestionController::class, 'create'])->name('question.create');
    Route::post('/ask-question', [QuestionController::class, 'store'])->name('question.store');
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

    // File download
    Route::get('/files/{file}/download', [QuestionAdminController::class, 'downloadFile'])->name('files.download');

    // Categories
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
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
