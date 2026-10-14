<?php

namespace App\Http\Controllers\Member;

use App\Enums\AttendanceStatus;
use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    /**
     * Display member attendance history and upcoming practice schedule.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // Upcoming open sessions
        $upcomingSessions = AttendanceSession::query()
            ->upcoming()
            ->take(5)
            ->get();

        // User's attendance records
        $myRecords = AttendanceRecord::query()
            ->where('user_id', $user->id)
            ->with('session')
            ->join('attendance_sessions', 'attendance_records.attendance_session_id', '=', 'attendance_sessions.id')
            ->orderBy('attendance_sessions.date', 'desc')
            ->select('attendance_records.*')
            ->paginate(15);

        // Stats summary for the user
        $totalSessions = AttendanceSession::where('date', '<=', now()->toDateString())->count();
        $attendedCount = AttendanceRecord::where('user_id', $user->id)->attended()->count();
        $excusedCount = AttendanceRecord::where('user_id', $user->id)->where('status', AttendanceStatus::Excused->value)->count();
        $sickCount = AttendanceRecord::where('user_id', $user->id)->where('status', AttendanceStatus::Sick->value)->count();
        $absentCount = AttendanceRecord::where('user_id', $user->id)->where('status', AttendanceStatus::Absent->value)->count();

        $attendanceRate = $totalSessions > 0
            ? round(($attendedCount / $totalSessions) * 100, 1)
            : 0;

        return view('member.attendance.index', [
            'user' => $user,
            'upcomingSessions' => $upcomingSessions,
            'records' => $myRecords,
            'stats' => [
                'total_sessions' => $totalSessions,
                'attended' => $attendedCount,
                'excused' => $excusedCount,
                'sick' => $sickCount,
                'absent' => $absentCount,
                'rate' => $attendanceRate,
            ],
        ]);
    }
}
