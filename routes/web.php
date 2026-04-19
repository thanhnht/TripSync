<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\TripController;
use Illuminate\Support\Facades\Route;

// ── Guest routes ───────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/register',          [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',         [AuthController::class, 'register']);

    Route::get('/login',             [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',            [AuthController::class, 'login']);

    Route::get('/forgot-password',   [ForgotPasswordController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password',  [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password',   [ForgotPasswordController::class, 'resetPassword'])->name('password.update');
});

// ── Authenticated routes ───────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Profile
    Route::get('/profile',             [AuthController::class, 'showProfile'])->name('profile');
    Route::put('/profile',             [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password',    [AuthController::class, 'updatePassword'])->name('profile.password');

    // Dashboard
    Route::get('/', [TripController::class, 'dashboard'])->name('dashboard');

    // Trips CRUD
    Route::resource('trips', TripController::class);

    // Join trip
    Route::get('/join',              [TripController::class, 'showJoin'])->name('trips.join');
    Route::post('/join',             [TripController::class, 'join'])->name('trips.join.post');

    // Trip actions
    Route::post('/trips/{trip}/invite/regenerate', [TripController::class, 'regenerateInvite'])->name('trips.invite.regenerate');
    Route::delete('/trips/{trip}/members/{user}',  [TripController::class, 'removeMember'])->name('trips.members.remove');
    Route::post('/trips/{trip}/leave',             [TripController::class, 'leaveTrip'])->name('trips.leave');
    Route::patch('/trips/{trip}/status',           [TripController::class, 'updateStatus'])->name('trips.status');
});
