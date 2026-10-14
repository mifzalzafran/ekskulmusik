<?php

use App\Http\Controllers\Admin\AttendanceSessionController as AdminAttendanceSessionController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Member\AttendanceController as MemberAttendanceController;
use App\Http\Controllers\Member\DashboardController as MemberDashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| School Music Extracurricular Management Application.
|
| Out of Scope (TODOs):
| TODO: Public registration is closed for now; students are registered by staff.
| TODO: Forgot / reset password functionality.
| TODO: Email verification workflow.
|
*/

// Root route: redirects guests to login, and authenticated users to their dashboard by role
Route::get('/', function (Request $request) {
    if (! $request->user()) {
        return redirect()->route('login');
    }

    return $request->user()->isAdmin()
        ? redirect()->route('admin.dashboard')
        : redirect()->route('member.dashboard');
});

// Guest-only authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

// Authenticated session routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
});

// Protected routes (middleware order: auth, auth.session, active, role:xxx)
Route::middleware(['auth', 'auth.session', 'active'])->group(function () {
    // Change password (available to both admin and member)
    Route::get('/password/change', [PasswordController::class, 'edit'])->name('password.edit');
    Route::put('/password/change', [PasswordController::class, 'update'])->name('password.update');

    // Admin dashboard
    Route::get('/admin/dashboard', AdminDashboardController::class)
        ->middleware('role:admin')
        ->name('admin.dashboard');

    // Member dashboard
    Route::get('/member/dashboard', MemberDashboardController::class)
        ->middleware('role:member')
        ->name('member.dashboard');

    // Member attendance & schedule
    Route::get('/member/attendance', [MemberAttendanceController::class, 'index'])
        ->middleware('role:member')
        ->name('member.attendance');
});

// Admin management routes (middleware order: auth, auth.session, active, role:admin)
Route::middleware(['auth', 'auth.session', 'active', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::patch('users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active')->withTrashed();
        Route::patch('users/{user}/toggle-choir', [UserController::class, 'toggleChoir'])->name('users.toggle-choir')->withTrashed();
        Route::patch('users/{user}/restore', [UserController::class, 'restore'])->name('users.restore')->withTrashed();
        Route::resource('users', UserController::class)->withTrashed(['show', 'edit', 'update', 'destroy']);

        // Attendance sessions management
        Route::patch('attendance-sessions/{session}/toggle-open', [AdminAttendanceSessionController::class, 'toggleOpen'])->name('attendance-sessions.toggle-open');
        Route::post('attendance-sessions/{session}/record', [AdminAttendanceSessionController::class, 'recordAttendance'])->name('attendance-sessions.record');
        Route::resource('attendance-sessions', AdminAttendanceSessionController::class)->parameters([
            'attendance-sessions' => 'session',
        ]);
    });

