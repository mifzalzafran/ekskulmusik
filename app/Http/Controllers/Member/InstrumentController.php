<?php

namespace App\Http\Controllers\Member;

use App\Enums\InstrumentStatus;
use App\Http\Controllers\Controller;
use App\Models\Instrument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class InstrumentController extends Controller
{
    /**
     * Toggle take-home status for a student's own deposited instrument.
     */
    public function toggleTakeHome(Request $request, Instrument $instrument): RedirectResponse
    {
        $user = $request->user();

        // Security check: Must belong to this student
        if ($instrument->owner_user_id !== $user->id) {
            abort(403, 'Anda bukan pemilik sah instrumen ini.');
        }

        if ($instrument->status === InstrumentStatus::InUse) {
            return back()->with('error', 'Alat musik ini sedang dipinjam/digunakan siswa lain di studio. Mohon tunggu alat dikembalikan sebelum dibawa pulang.');
        }

        if ($instrument->status === InstrumentStatus::Available) {
            $instrument->update([
                'status' => InstrumentStatus::TakenHome,
            ]);

            return back()->with('status', "{$instrument->name} berhasil ditandai: Dibawa Pulang ke Rumah.");
        }

        if ($instrument->status === InstrumentStatus::TakenHome) {
            $instrument->update([
                'status' => InstrumentStatus::Available,
            ]);

            return back()->with('status', "{$instrument->name} berhasil ditandai: Dititipkan Kembali di Studio.");
        }

        return back()->with('error', 'Status instrumen tidak dapat diubah saat ini.');
    }
}
