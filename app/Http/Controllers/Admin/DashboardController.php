<?php

namespace App\Http\Controllers\Admin;

use App\Enums\InstrumentStatus;
use App\Http\Controllers\Controller;
use App\Models\AttendanceSession;
use App\Models\CashTransaction;
use App\Models\Event;
use App\Models\Instrument;
use App\Models\InstrumentLoan;
use App\Models\StudioBooking;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the admin dashboard with comprehensive extracurricular metrics and operational data.
     */
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        $stats = [
            'total_members' => User::members()->active()->count(),
            'choir_members' => User::members()->active()->choir()->count(),
            'open_sessions_count' => AttendanceSession::open()->count(),
            'total_instruments' => Instrument::count(),
            'instruments_available' => Instrument::available()->count(),
            'instruments_in_use' => Instrument::where('status', InstrumentStatus::InUse->value)->count(),
            'instruments_taken_home' => Instrument::where('status', InstrumentStatus::TakenHome->value)->count(),
            'current_cash' => CashTransaction::currentBalance(),
            'pending_bookings_count' => StudioBooking::pending()->count(),
        ];

        // Today's studio bookings
        $todayBookings = StudioBooking::where('booking_date', now()->toDateString())
            ->with(['room', 'user'])
            ->orderBy('start_time')
            ->get();

        // Pending studio booking requests needing approval
        $pendingBookings = StudioBooking::pending()
            ->with(['room', 'user'])
            ->latest()
            ->take(6)
            ->get();

        // Recent cash transactions
        $recentTransactions = CashTransaction::with(['user', 'recorder'])
            ->latest('transaction_date')
            ->latest('id')
            ->take(5)
            ->get();

        // Upcoming events (demo, lomba, SAF/pensi)
        $upcomingEvents = Event::upcoming()
            ->withCount('participants')
            ->take(4)
            ->get();

        // Active open attendance sessions
        $activeSessions = AttendanceSession::open()
            ->withCount('records')
            ->latest('date')
            ->take(3)
            ->get();

        // Ongoing instrument loans (who is playing what in studio)
        $activeLoans = InstrumentLoan::active()
            ->with(['instrument.owner', 'borrower', 'handler'])
            ->latest('borrowed_at')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'user',
            'stats',
            'todayBookings',
            'pendingBookings',
            'recentTransactions',
            'upcomingEvents',
            'activeSessions',
            'activeLoans'
        ));
    }
}
