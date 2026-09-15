<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GameController;
use Illuminate\Support\Facades\Route;

// YB - 15-09-2026 Authentication API routes with session support
Route::prefix('auth')->middleware(['web'])->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('api.auth.login');
    Route::post('/register', [AuthController::class, 'register'])->name('api.auth.register');
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.auth.logout');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('api.auth.forgot-password');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('api.auth.reset-password');
    Route::post('/change-password', [AuthController::class, 'changePassword'])->name('api.auth.change-password');
    Route::get('/user', [AuthController::class, 'user'])->name('api.auth.user');
});

// YB - 15-09-2026 Game API routes with rate limiting protection (60 requests per minute)
Route::prefix('game')->middleware('throttle:60,1')->group(function () {
    Route::post('/start', [GameController::class, 'start'])->name('api.game.start');
    Route::post('/{game}/guess', [GameController::class, 'guess'])->name('api.game.guess');
    Route::get('/{game}', [GameController::class, 'show'])->name('api.game.show');
});
