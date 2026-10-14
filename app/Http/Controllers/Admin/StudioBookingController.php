<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StudioBookingStatus;
use App\Http\Controllers\Controller;
use App\Models\StudioBooking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudioBookingController extends Controller
{
    /**
     * Update the approval or execution status of a studio booking.
     */
    public function updateStatus(Request $request, StudioBooking $booking): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::enum(StudioBookingStatus::class)],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $booking->update([
            'status' => $validated['status'],
            'approved_by' => $request->user()->id,
            'notes' => $validated['notes'] ?? $booking->notes,
        ]);

        $label = $booking->status->label();

        return back()->with('status', "Status booking studio berhasil diperbarui menjadi: {$label}.");
    }
}
