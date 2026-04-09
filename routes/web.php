<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MailManagementController;
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
Route::post('/contact', [HomeController::class, 'submitContact'])->name('contact.submit');

// Fatwa Archive
Route::get('/fatwa-archive', [FatwaController::class, 'index'])->name('fatwa.archive');
Route::get('/fatwa-archive/{fatwa}', [FatwaController::class, 'show'])->name('fatwa.show');
Route::get('/mufti-archive/{user}', [FatwaController::class, 'muftiArchive'])->name('fatwa.mufti.archive');
Route::post('/fatwa-archive/{fatwa}/like', [FatwaController::class, 'like'])->name('fatwa.like');

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/login/check-account', [LoginController::class, 'checkAccount'])->name('login.check');
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
    Route::put('/user/profile', [\App\Http\Controllers\User\UserDashboardController::class, 'updateProfile'])->name('user.profile.update');
    Route::put('/user/password', [\App\Http\Controllers\User\UserDashboardController::class, 'updatePassword'])->name('user.password.update');
    Route::get('/user/questions/{question}', [\App\Http\Controllers\User\UserDashboardController::class, 'show'])->name('user.question.show');
    Route::get('/ask-question', [\App\Http\Controllers\QuestionController::class, 'create'])->name('question.create');
    Route::post('/ask-question', [\App\Http\Controllers\QuestionController::class, 'store'])->name('question.store');

    // AJAX File Uploading
    Route::post('/question-files/upload', [\App\Http\Controllers\QuestionFileController::class, 'upload'])->name('question.files.upload');
    Route::post('/question-files/delete', [\App\Http\Controllers\QuestionFileController::class, 'delete'])->name('question.files.delete');

    // Global File Downloads (Access restricted to authenticated users)
    Route::get('/files/{file}/download', [\App\Http\Controllers\Admin\QuestionAdminController::class, 'downloadFile'])->name('files.download');
    Route::get('/files/{file}/view', [\App\Http\Controllers\Admin\QuestionAdminController::class, 'viewFile'])->name('files.view');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/visitor-insights', [DashboardController::class, 'visitorInsights'])->name('visitor.insights');

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

    Route::post('/questions/{question}/history/comment', [QuestionAdminController::class, 'addHistoryComment'])->name('history.comment');

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

    // Site Settings
    Route::get('/system-settings', [DashboardController::class, 'systemControl'])->name('settings.system');
    Route::post('/settings/update', [DashboardController::class, 'updateSettings'])->name('settings.update');

    // Mailing Management (Sprite)
    Route::get('/mail-management', [MailManagementController::class, 'index'])->name('mail-management.index');
    Route::get('/mail-management/{mufti}', [MailManagementController::class, 'show'])->name('mail-management.show');
    Route::post('/mail-management/{mufti}/send', [MailManagementController::class, 'sendNow'])->name('mail-management.send');
});

/*
|--------------------------------------------------------------------------
| Mufti Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:mufti'])->prefix('mufti')->name('mufti.')->group(function () {
    Route::get('/dashboard', [MuftiController::class, 'dashboard'])->name('dashboard');
    Route::get('/questions', [MuftiController::class, 'questions'])->name('questions');
    Route::get('/questions/pending', [MuftiController::class, 'pendingQuestions'])->name('questions.pending');
    Route::get('/questions/{question}', [MuftiController::class, 'showQuestion'])->name('questions.show');
    Route::post('/questions/{question}/answer', [MuftiController::class, 'submitAnswer'])->name('questions.answer');
    Route::get('/profile', [MuftiController::class, 'profile'])->name('profile');
    Route::put('/profile', [MuftiController::class, 'updateProfile'])->name('profile.update');

    // New History & Communication Gateway for Muftis
    Route::get('/questions/{question}/history', [QuestionAdminController::class, 'history'])->name('questions.history');
    Route::post('/questions/{question}/history/comment', [QuestionAdminController::class, 'addHistoryComment'])->name('questions.history.comment');
});



