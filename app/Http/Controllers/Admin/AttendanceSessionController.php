<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AttendanceSessionType;
use App\Enums\AttendanceStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RecordAttendanceRequest;
use App\Http\Requests\Admin\StoreAttendanceSessionRequest;
use App\Http\Requests\Admin\UpdateAttendanceSessionRequest;
use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AttendanceSessionController extends Controller
{
    /**
     * Display a listing of attendance sessions with filters.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', AttendanceSession::class);

        $query = AttendanceSession::query()->with('creator')->withCount('records');

        // Filter: session type
        if ($request->filled('type')) {
            $type = $request->input('type');
            if (AttendanceSessionType::tryFrom($type)) {
                $query->where('session_type', $type);
            }
        }

        // Filter: is_open
        if ($request->has('is_open') && $request->input('is_open') !== '' && $request->input('is_open') !== null) {
            $query->where('is_open', $request->boolean('is_open'));
        }

        // Search: title, location, description
        if ($request->filled('search')) {
            $term = $request->input('search');
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', "%{$term}%")
                    ->orWhere('location', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%");
            });
        }

        $sessions = $query->latest('date')->latest('id')->paginate(15)->withQueryString();

        return view('admin.attendance.index', [
            'sessions' => $sessions,
            'filters' => $request->only(['type', 'is_open', 'search']),
        ]);
    }

    /**
     * Show the form for creating a new attendance session.
     */
    public function create(): View
    {
        Gate::authorize('create', AttendanceSession::class);

        return view('admin.attendance.create');
    }

    /**
     * Store a newly created attendance session.
     */
    public function store(StoreAttendanceSessionRequest $request): RedirectResponse
    {
        Gate::authorize('create', AttendanceSession::class);

        $data = $request->validated();
        $data['created_by'] = $request->user()->id;

        $session = AttendanceSession::create($data);

        return redirect()
            ->route('admin.attendance-sessions.show', $session)
            ->with('status', 'Sesi presensi berhasil dibuat.');
    }

    /**
     * Display the specified attendance session with members checklist.
     */
    public function show(AttendanceSession $session): View
    {
        Gate::authorize('view', $session);

        $session->load(['creator', 'records.user.musicRoles', 'records.recorder']);

        // Fetch all active members to record attendance
        $activeMembers = User::query()
            ->members()
            ->active()
            ->with('musicRoles')
            ->orderBy('name')
            ->get();

        // Index existing records by user_id
        $recordsByUser = $session->records->keyBy('user_id');

        return view('admin.attendance.show', [
            'session' => $session,
            'members' => $activeMembers,
            'recordsByUser' => $recordsByUser,
            'counts' => $session->countsByStatus(),
        ]);
    }

    /**
     * Show the form for editing the attendance session.
     */
    public function edit(AttendanceSession $session): View
    {
        Gate::authorize('update', $session);

        return view('admin.attendance.edit', compact('session'));
    }

    /**
     * Update the specified attendance session.
     */
    public function update(UpdateAttendanceSessionRequest $request, AttendanceSession $session): RedirectResponse
    {
        Gate::authorize('update', $session);

        $session->update($request->validated());

        return redirect()
            ->route('admin.attendance-sessions.show', $session)
            ->with('status', 'Sesi presensi berhasil diperbarui.');
    }

    /**
     * Remove the specified attendance session.
     */
    public function destroy(AttendanceSession $session): RedirectResponse
    {
        Gate::authorize('delete', $session);

        $session->delete();

        return redirect()
            ->route('admin.attendance-sessions.index')
            ->with('status', 'Sesi presensi berhasil dihapus.');
    }

    /**
     * Toggle the open/closed status of the attendance session.
     */
    public function toggleOpen(AttendanceSession $session): RedirectResponse
    {
        Gate::authorize('toggleOpen', $session);

        $session->update(['is_open' => ! $session->is_open]);

        $message = $session->is_open ? 'Sesi presensi berhasil dibuka.' : 'Sesi presensi berhasil ditutup.';

        return back()->with('status', $message);
    }

    /**
     * Record attendance checklist for all submitted members.
     */
    public function recordAttendance(RecordAttendanceRequest $request, AttendanceSession $session): RedirectResponse
    {
        Gate::authorize('recordAttendance', $session);

        if (! $session->is_open) {
            return back()->with('error', 'Sesi presensi ini telah ditutup.');
        }

        $attendances = $request->input('attendances', []);
        $recorderId = $request->user()->id;

        DB::transaction(function () use ($session, $attendances, $recorderId) {
            foreach ($attendances as $row) {
                $userId = $row['user_id'] ?? null;
                $status = $row['status'] ?? AttendanceStatus::Present->value;
                $notes = $row['notes'] ?? null;

                if ($userId) {
                    AttendanceRecord::updateOrCreate(
                        [
                            'attendance_session_id' => $session->id,
                            'user_id' => $userId,
                        ],
                        [
                            'status' => $status,
                            'notes' => $notes,
                            'recorded_at' => now(),
                            'recorded_by' => $recorderId,
                        ]
                    );
                }
            }
        });

        return back()->with('status', 'Presensi anggota berhasil disimpan.');
    }
}
