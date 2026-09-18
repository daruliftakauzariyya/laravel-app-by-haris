<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ImportOldFatwaController;
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
Route::get('/team', [HomeController::class, 'team'])->name('team');

// Fatwa Archive & Short URL
Route::get('/f/{code}', [FatwaController::class, 'shortUrlRedirect'])->name('fatwa.short');
Route::get('/fatwa-archive', [FatwaController::class, 'index'])->name('fatwa.archive');
Route::get('/fatwa-archive/{fatwa}', [FatwaController::class, 'show'])->name('fatwa.show');
Route::get('/fatwa/madhab', [FatwaController::class, 'madhabArchive'])->name('fatwa.madhab');
Route::get('/mufti-archive/{user}', [FatwaController::class, 'muftiArchive'])->name('fatwa.mufti.archive');
Route::post('/fatwa-archive/{fatwa}/like', [FatwaController::class, 'like'])->name('fatwa.like');
Route::get('/fatwas/mention-search', [FatwaController::class, 'mentionSearch'])->name('fatwa.mention.search');
Route::get('/privacy', [HomeController::class, 'privacy'])->name('privacy');
Route::get('/terms', [HomeController::class, 'terms'])->name('terms');
Route::get('/sitemap.xml', [HomeController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [HomeController::class, 'robotsTxt'])->name('robots.txt');

// Live Activity Signal Heartbeat
Route::match(['get', 'post'], '/user-heartbeat', function () {
    if (auth()->check()) {
        \App\Models\User::where('id', auth()->id())->update([
            'last_seen_at' => now(),
        ]);
        return response()->json(['status' => 'ok', 'online' => true, 'time' => now()->toIso8601String()]);
    }
    return response()->json(['status' => 'guest']);
})->name('user.heartbeat');

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


// Google OAuth Authentication
Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('auth.google.callback');
Route::post('/auth/google/token', [GoogleController::class, 'handleToken'])->name('auth.google.token');

// OTP Verification
Route::get('/verify-otp', [\App\Http\Controllers\Auth\OtpVerificationController::class, 'showVerifyForm'])->name('otp.verify.form');
Route::post('/verify-otp', [\App\Http\Controllers\Auth\OtpVerificationController::class, 'verify'])->name('otp.verify.submit');

// OTP Login
Route::post('/login/otp/send', [\App\Http\Controllers\Auth\LoginController::class, 'sendLoginOtp'])->name('login.otp.send');
Route::post('/login/otp/verify', [\App\Http\Controllers\Auth\LoginController::class, 'verifyLoginOtp'])->name('login.otp.verify');

// Password Recovery (Forgot Password)
Route::get('/forgot-password', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetOtp'])->name('password.email');
Route::get('/reset-password/{email}', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'reset'])->name('password.update');

/*
|--------------------------------------------------------------------------
| Questioner Routes (auth)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/user/dashboard', [\App\Http\Controllers\User\UserDashboardController::class, 'index'])->name('user.dashboard');
    Route::get('/user/settings', [\App\Http\Controllers\User\UserDashboardController::class, 'settings'])->name('user.settings');
    Route::get('/user/auth/google/link', [GoogleController::class, 'link'])->name('user.google.link');
    Route::post('/user/auth/google/unlink', [GoogleController::class, 'unlink'])->name('user.google.unlink');
    Route::put('/user/profile', [\App\Http\Controllers\User\UserDashboardController::class, 'updateProfile'])->name('user.profile.update');
    Route::put('/user/password', [\App\Http\Controllers\User\UserDashboardController::class, 'updatePassword'])->name('user.password.update');
    Route::get('/user/questions/{question}', [\App\Http\Controllers\User\UserDashboardController::class, 'show'])->name('user.question.show');
    Route::get('/ask-question', [\App\Http\Controllers\QuestionController::class, 'create'])->name('question.create');
    Route::post('/ask-question', [\App\Http\Controllers\QuestionController::class, 'store'])->name('question.store');
    Route::get('/complete-profile', [ProfileController::class, 'showCompleteForm'])->name('profile.complete');
    Route::post('/complete-profile', [ProfileController::class, 'complete'])->name('profile.complete.store');
    Route::post('/complete-profile/skip', [ProfileController::class, 'skip'])->name('profile.complete.skip');

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

    // Import Old Fatwas & PDF Export
    Route::get('/fatwas/export-pdf', [DashboardController::class, 'exportPdf'])->name('fatwas.export.pdf');
    Route::get('/fatwas/import-old', [ImportOldFatwaController::class, 'create'])->name('fatwas.import-old');
    Route::post('/fatwas/import-old', [ImportOldFatwaController::class, 'store'])->name('fatwas.import-old.store');
    Route::post('/fatwas/quick-create-questioner', [ImportOldFatwaController::class, 'quickCreateQuestioner'])->name('fatwas.quick-create-questioner');

    // Questions
    Route::get('/questions', [QuestionAdminController::class, 'index'])->name('questions.index');
    Route::get('/questions/{question}', [QuestionAdminController::class, 'show'])->name('questions.show');
    Route::get('/questions/{question}/accept', [QuestionAdminController::class, 'showAcceptForm'])->name('questions.accept.form');
    Route::post('/questions/{question}/accept', [QuestionAdminController::class, 'accept'])->name('questions.accept');
    Route::post('/questions/{question}/assign', [QuestionAdminController::class, 'assign'])->name('questions.assign');

    // Admin write answer
    Route::get('/questions/{question}/answer', [QuestionAdminController::class, 'createAnswer'])->name('questions.answer');
    Route::post('/questions/{question}/answer', [QuestionAdminController::class, 'storeAnswer'])->name('questions.answer.store');

    // Verification
    Route::get('/questions/{question}/verify', [QuestionAdminController::class, 'verify'])->name('questions.verify');
    Route::post('/questions/{question}/approve', [QuestionAdminController::class, 'approve'])->name('questions.approve');
    Route::post('/questions/{question}/toggle-public', [QuestionAdminController::class, 'togglePublicVisibility'])->name('questions.toggle-public');
    Route::post('/questions/{question}/unpublish', [QuestionAdminController::class, 'unpublish'])->name('questions.unpublish');
    Route::post('/questions/{question}/reject', [QuestionAdminController::class, 'reject'])->name('questions.reject');
    Route::post('/questions/{question}/correction', [QuestionAdminController::class, 'requestCorrection'])->name('questions.correction');
    Route::get('/questions/{question}/history', [QuestionAdminController::class, 'history'])->name('questions.history');

    Route::post('/questions/{question}/history/comment', [QuestionAdminController::class, 'addHistoryComment'])->name('history.comment');

    // Categories
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Madhabs
    Route::get('/madhabs', [\App\Http\Controllers\Admin\MadhabController::class, 'index'])->name('madhabs.index');
    Route::post('/madhabs', [\App\Http\Controllers\Admin\MadhabController::class, 'store'])->name('madhabs.store');
    Route::put('/madhabs/{madhab}', [\App\Http\Controllers\Admin\MadhabController::class, 'update'])->name('madhabs.update');
    Route::delete('/madhabs/{madhab}', [\App\Http\Controllers\Admin\MadhabController::class, 'destroy'])->name('madhabs.destroy');

    // Mufti Management
    Route::get('/muftis', [\App\Http\Controllers\Admin\MuftiManagementController::class, 'index'])->name('muftis.index');
    Route::get('/muftis/create', [\App\Http\Controllers\Admin\MuftiManagementController::class, 'create'])->name('muftis.create');
    Route::post('/muftis', [\App\Http\Controllers\Admin\MuftiManagementController::class, 'store'])->name('muftis.store');
    Route::get('/muftis/{user}', [\App\Http\Controllers\Admin\MuftiManagementController::class, 'show'])->name('muftis.show');
    Route::get('/muftis/{user}/edit', [\App\Http\Controllers\Admin\MuftiManagementController::class, 'edit'])->name('muftis.edit');
    Route::put('/muftis/{user}', [\App\Http\Controllers\Admin\MuftiManagementController::class, 'update'])->name('muftis.update');
    Route::delete('/muftis/{user}', [\App\Http\Controllers\Admin\MuftiManagementController::class, 'destroy'])->name('muftis.destroy');
    Route::patch('/muftis/{user}/toggle-status', [\App\Http\Controllers\Admin\MuftiManagementController::class, 'toggleStatus'])->name('muftis.toggle-status');
    Route::get('/muftis/{user}/activity', [\App\Http\Controllers\Admin\MuftiManagementController::class, 'activity'])->name('muftis.activity');
    Route::get('/muftis/{user}/history', [\App\Http\Controllers\Admin\MuftiManagementController::class, 'history'])->name('muftis.history');
    Route::post('/muftis/{user}/approve-avatar', [\App\Http\Controllers\Admin\MuftiManagementController::class, 'approveAvatar'])->name('muftis.approve-avatar');
    Route::post('/muftis/{user}/reject-avatar', [\App\Http\Controllers\Admin\MuftiManagementController::class, 'rejectAvatar'])->name('muftis.reject-avatar');

    // Reports & Analytics Center
    Route::get('/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [\App\Http\Controllers\Admin\ReportController::class, 'export'])->name('reports.export');

    // Questioner Management
    Route::get('/questioners', [\App\Http\Controllers\Admin\QuestionerController::class, 'index'])->name('questioners.index');
    Route::get('/questioners/{user}', [\App\Http\Controllers\Admin\QuestionerController::class, 'show'])->name('questioners.show');

    // Site Settings
    Route::get('/system-settings', [DashboardController::class, 'systemControl'])->name('settings.system');
    Route::post('/settings/update', [DashboardController::class, 'updateSettings'])->name('settings.update');

    // Maintenance
    Route::get('/maintenance/clear-cache', [DashboardController::class, 'clearCache'])->name('maintenance.clear-cache');

    // Mailing Management (Sprite)
    Route::get('/mail-management', [MailManagementController::class, 'index'])->name('mail-management.index');
    Route::get('/mail-management/{mufti}', [MailManagementController::class, 'show'])->name('mail-management.show');
    Route::post('/mail-management/{mufti}/send', [MailManagementController::class, 'sendNow'])->name('mail-management.send');

    // Real-time API
    Route::get('/api/live-stats', [DashboardController::class, 'liveUsersApi'])->name('live-stats.api');

    // Featured Fatwas Rules
    Route::get('/featured-rules', [\App\Http\Controllers\Admin\FeaturedRuleController::class, 'index'])->name('featured-rules.index');
    Route::get('/featured-rules/create', [\App\Http\Controllers\Admin\FeaturedRuleController::class, 'create'])->name('featured-rules.create');
    Route::post('/featured-rules', [\App\Http\Controllers\Admin\FeaturedRuleController::class, 'store'])->name('featured-rules.store');
    Route::get('/featured-rules/{featuredRule}/edit', [\App\Http\Controllers\Admin\FeaturedRuleController::class, 'edit'])->name('featured-rules.edit');
    Route::put('/featured-rules/{featuredRule}', [\App\Http\Controllers\Admin\FeaturedRuleController::class, 'update'])->name('featured-rules.update');
    Route::delete('/featured-rules/{featuredRule}', [\App\Http\Controllers\Admin\FeaturedRuleController::class, 'destroy'])->name('featured-rules.destroy');
    Route::patch('/featured-rules/{featuredRule}/toggle', [\App\Http\Controllers\Admin\FeaturedRuleController::class, 'toggleActive'])->name('featured-rules.toggle');
});

/*
|--------------------------------------------------------------------------
| Mufti Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:mufti'])->prefix('mufti')->name('mufti.')->group(function () {
    Route::get('/dashboard', [MuftiController::class, 'dashboard'])->name('dashboard');
    Route::get('/search', [MuftiController::class, 'search'])->name('search');
    Route::get('/api/search', [MuftiController::class, 'liveSearch'])->name('api.search');
    Route::get('/fatwas/{question}', [MuftiController::class, 'readFatwa'])->name('fatwa.read');
    Route::get('/questions', [MuftiController::class, 'questions'])->name('questions');
    Route::get('/questions/pending', [MuftiController::class, 'pendingQuestions'])->name('questions.pending');
    Route::get('/questions/published', [MuftiController::class, 'publishedQuestions'])->name('questions.published');
    Route::get('/questions/{question}', [MuftiController::class, 'showQuestion'])->name('questions.show');
    Route::post('/questions/{question}/answer', [MuftiController::class, 'submitAnswer'])->name('questions.answer');
    Route::post('/questions/{question}/draft', [MuftiController::class, 'saveDraft'])->name('questions.draft');
    Route::get('/profile', [MuftiController::class, 'profile'])->name('profile');
    Route::put('/profile', [MuftiController::class, 'updateProfile'])->name('profile.update');
    Route::get('/reports', [MuftiController::class, 'reports'])->name('reports');
    Route::get('/reports/export', [MuftiController::class, 'exportReports'])->name('reports.export');

    // New History & Communication Gateway for Muftis
    Route::get('/questions/{question}/history', [QuestionAdminController::class, 'history'])->name('questions.history');
    Route::post('/questions/{question}/history/comment', [QuestionAdminController::class, 'addHistoryComment'])->name('questions.history.comment');
});