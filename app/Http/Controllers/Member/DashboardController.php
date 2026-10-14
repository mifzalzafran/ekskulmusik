<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSession;
use App\Models\Event;
use App\Models\Instrument;
use App\Models\StudioBooking;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the member dashboard with personalized attendance, deposited gear, studio bookings, and events.
     */
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $user->load(['musicRoles', 'ownedInstruments.currentLoan.borrower']);

        // Attendance stats
        $totalSessions = AttendanceSession::where('date', '<=', now()->toDateString())->count();
        $attendedCount = $user->attendanceRecords()->attended()->count();
        $attendanceRate = $totalSessions > 0
            ? round(($attendedCount / $totalSessions) * 100, 1)
            : 0;

        // Upcoming rehearsals / ceremony sessions
        $upcomingSessions = AttendanceSession::upcoming()
            ->take(3)
            ->get();

        // My deposited instruments at studio
        $myInstruments = $user->ownedInstruments;

        // My studio bookings
        $myBookings = StudioBooking::where('user_id', $user->id)
            ->with(['room', 'approver'])
            ->latest('booking_date')
            ->take(5)
            ->get();

        // Available studio instruments to play
        $availableInstruments = Instrument::available()
            ->with('owner')
            ->take(6)
            ->get();

        // Upcoming events
        $upcomingEvents = Event::upcoming()
            ->take(3)
            ->get();

        // Events this user is participating in
        $myEvents = $user->events()
            ->where('start_date', '>=', now()->toDateString())
            ->get();

        return view('member.dashboard', compact(
            'user',
            'attendanceRate',
            'attendedCount',
            'totalSessions',
            'upcomingSessions',
            'myInstruments',
            'myBookings',
            'availableInstruments',
            'upcomingEvents',
            'myEvents'
        ));
    }
}
