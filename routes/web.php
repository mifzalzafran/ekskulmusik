<?php

use App\Http\Controllers\Admin\AttendanceSessionController as AdminAttendanceSessionController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\StudioBookingController as AdminStudioBookingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Member\AttendanceController as MemberAttendanceController;
use App\Http\Controllers\Member\DashboardController as MemberDashboardController;
use App\Http\Controllers\Member\InstrumentController as MemberInstrumentController;
use App\Models\Event;
use App\Models\Instrument;
use App\Models\StudioRoom;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| School Music Extracurricular Management Application.
|
*/

// Root route: Shows public landing page for guests, redirects authenticated users to their dashboard
Route::get('/', function (Request $request) {
    if ($request->user()) {
        return $request->user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('member.dashboard');
    }

    $upcomingEvents = Event::upcoming()->take(3)->get();
    $studioRooms = StudioRoom::active()->get();
    $stats = [
        'members_count' => User::members()->active()->count(),
        'choir_count' => User::members()->active()->choir()->count(),
        'instruments_count' => Instrument::count(),
        'studio_rooms_count' => StudioRoom::active()->count(),
    ];

    return view('welcome', compact('upcomingEvents', 'studioRooms', 'stats'));
})->name('home');

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

    // Member deposited instrument take-home toggle
    Route::patch('/member/instruments/{instrument}/toggle-take-home', [MemberInstrumentController::class, 'toggleTakeHome'])
        ->middleware('role:member')
        ->name('member.instruments.toggle-take-home');
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

        // Studio booking management status update
        Route::patch('studio-bookings/{booking}/status', [AdminStudioBookingController::class, 'updateStatus'])->name('studio-bookings.status');

        // Attendance sessions management
        Route::patch('attendance-sessions/{session}/toggle-open', [AdminAttendanceSessionController::class, 'toggleOpen'])->name('attendance-sessions.toggle-open');
        Route::post('attendance-sessions/{session}/record', [AdminAttendanceSessionController::class, 'recordAttendance'])->name('attendance-sessions.record');
        Route::resource('attendance-sessions', AdminAttendanceSessionController::class)->parameters([
            'attendance-sessions' => 'session',
        ]);
    });

