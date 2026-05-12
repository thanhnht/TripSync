<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Checklist\ChecklistController;
use App\Http\Controllers\Expense\ExpenseController;
use App\Http\Controllers\Photo\PhotoController;
use App\Http\Controllers\Schedule\ActivityController;
use App\Http\Controllers\Schedule\ScheduleController;
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
    // Checklist routes
    Route::prefix('trips/{trip}/checklist')->name('checklist.')->group(function () {
        Route::get('/',              [ChecklistController::class, 'index'])->name('index');
        Route::post('/',             [ChecklistController::class, 'store'])->name('store');
        Route::put('/{item}',        [ChecklistController::class, 'update'])->name('update');
        Route::delete('/{item}',     [ChecklistController::class, 'destroy'])->name('destroy');
        Route::patch('/{item}/toggle', [ChecklistController::class, 'toggle'])->name('toggle');
    });

    // Photo routes
    Route::prefix('trips/{trip}/photos')->name('photo.')->group(function () {
        Route::get('/',              [PhotoController::class, 'index'])->name('index');
        Route::post('/',             [PhotoController::class, 'store'])->name('store');
        Route::delete('/{photo}',    [PhotoController::class, 'destroy'])->name('destroy');
        Route::get('/{photo}/download',  [PhotoController::class, 'download'])->name('download');
        Route::post('/download-bulk',    [PhotoController::class, 'downloadBulk'])->name('download-bulk');
    });

    // Expense routes
    Route::prefix('trips/{trip}/expenses')->name('expense.')->group(function () {
        Route::get('/',        [ExpenseController::class, 'index'])->name('index');
        Route::post('/',       [ExpenseController::class, 'store'])->name('store');
        Route::put('/{expense}',    [ExpenseController::class, 'update'])->name('update');
        Route::delete('/{expense}', [ExpenseController::class, 'destroy'])->name('destroy');
        Route::post('/import', [ExpenseController::class, 'importFromSchedule'])->name('import');
    });

    Route::prefix('trips/{trip}/schedule')->name('schedule.')->group(function () {

        // Xem toàn bộ lịch trình
        Route::get('/',                          [ScheduleController::class, 'index'])->name('index');

        // Cập nhật ghi chú ngày
        Route::patch('/days/{day}',              [ScheduleController::class, 'updateDay'])->name('day.update');

        // Sắp xếp lại hoạt động (AJAX)
        Route::post('/reorder',                  [ScheduleController::class, 'reorder'])->name('reorder');

        // Thêm hoạt động vào 1 ngày
        Route::post('/days/{day}/activities',    [ActivityController::class, 'store'])->name('activities.store');

        // Sửa hoạt động
        Route::put('/activities/{activity}',     [ActivityController::class, 'update'])->name('activities.update');

        // Xoá hoạt động
        Route::delete('/activities/{activity}',  [ActivityController::class, 'destroy'])->name('activities.destroy');

        // Duyệt / từ chối (owner only)
        Route::patch('/activities/{activity}/approve', [ActivityController::class, 'approve'])->name('activities.approve');
        Route::patch('/activities/{activity}/reject',  [ActivityController::class, 'reject'])->name('activities.reject');

        // Bình chọn (AJAX)
        Route::post('/activities/{activity}/vote',     [ActivityController::class, 'vote'])->name('activities.vote');

        // Comment
        Route::post('/activities/{activity}/comments',       [ActivityController::class, 'comment'])->name('activities.comment');
        Route::delete('/comments/{comment}',                 [ActivityController::class, 'destroyComment'])->name('comments.destroy');
    });
});
