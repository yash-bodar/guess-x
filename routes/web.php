<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GameController;
use Illuminate\Support\Facades\Route;

// YB - 15-09-2026 Main game web route
Route::get('/', [GameController::class, 'index'])->name('game.index');

// YB - 15-09-2026 Google OAuth Authentication routes
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
