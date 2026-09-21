<?php

use App\Http\Controllers\Api\Mufti\MuftiAuthApiController;
use App\Http\Controllers\Api\Mufti\MuftiDashboardApiController;
use App\Http\Controllers\Api\Mufti\MuftiProfileApiController;
use App\Http\Controllers\Api\Mufti\MuftiQuestionApiController;
use App\Http\Controllers\Api\Mufti\MuftiReportApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Mufti Mobile App API Routes
|--------------------------------------------------------------------------
| Dedicated API endpoints for Darul Ifta Mufti Mobile Application
*/

Route::prefix('mufti')->group(function () {
    // Single-Method OTP Authentication
    Route::post('/send-otp', [MuftiAuthApiController::class, 'sendOtp']);
    Route::post('/verify-otp', [MuftiAuthApiController::class, 'verifyOtp']);

    // Legacy Password Login
    Route::post('/login', [MuftiAuthApiController::class, 'login']);

    // Protected Scholar Routes
    Route::middleware('auth:sanctum')->group(function () {
        // Real-Time Incoming Assignment Call Check
        Route::get('/check-incoming-calls', [MuftiQuestionApiController::class, 'checkIncomingCalls']);
        Route::post('/questions/{id}/mark-opened', [MuftiQuestionApiController::class, 'markOpened']);

        // Auth & Identity
        Route::get('/me', [MuftiAuthApiController::class, 'me']);
        Route::post('/logout', [MuftiAuthApiController::class, 'logout']);

        // Dashboard
        Route::get('/dashboard', [MuftiDashboardApiController::class, 'index']);

        // Inquiries & Scholarly Work
        Route::get('/questions', [MuftiQuestionApiController::class, 'index']);
        Route::get('/questions/{id}', [MuftiQuestionApiController::class, 'show']);
        Route::post('/questions/{id}/answer', [MuftiQuestionApiController::class, 'submitAnswer']);
        Route::post('/questions/{id}/draft', [MuftiQuestionApiController::class, 'saveDraft']);

        // Reports & Analytics
        Route::get('/reports', [MuftiReportApiController::class, 'index']);

        // Profile & Settings
        Route::get('/profile', [MuftiProfileApiController::class, 'show']);
        Route::post('/profile/update', [MuftiProfileApiController::class, 'update']);
    });
});
