<?php

namespace Database\Seeders;

use App\Enums\AttendanceSessionType;
use App\Enums\AttendanceStatus;
use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\User;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::admins()->first();
        $members = User::members()->get();

        if ($members->isEmpty()) {
            return;
        }

        // 1. Sesi Lampau 1 (2 minggu lalu)
        $pastSession1 = AttendanceSession::updateOrCreate(
            ['title' => 'Latihan Rutin Aransemen Vokal & Band #1'],
            [
                'session_type' => AttendanceSessionType::Latihan,
                'date' => now()->subWeeks(2)->toDateString(),
                'start_time' => '15:30',
                'end_time' => '17:30',
                'location' => 'Studio Musik Sekolah',
                'description' => 'Latihan pembagian part vokal, kunci lagu, dan tempo drum.',
                'is_open' => false,
                'created_by' => $admin?->id,
            ]
        );

        // 2. Sesi Lampau 2 (1 minggu lalu)
        $pastSession2 = AttendanceSession::updateOrCreate(
            ['title' => 'Gladi Bersih Aubade Upacara Bendera'],
            [
                'session_type' => AttendanceSessionType::Upacara,
                'date' => now()->subWeek()->toDateString(),
                'start_time' => '07:00',
                'end_time' => '08:30',
                'location' => 'Lapangan Utama Sekolah',
                'description' => 'Gladi persiapan paduan suara lagu Indonesia Raya dan Mengheningkan Cipta.',
                'is_open' => false,
                'created_by' => $admin?->id,
            ]
        );

        // 3. Sesi Hari Ini (Buka untuk presensi)
        $todaySession = AttendanceSession::updateOrCreate(
            ['title' => 'Latihan Rutin Persiapan Pentas Musik'],
            [
                'session_type' => AttendanceSessionType::Latihan,
                'date' => now()->toDateString(),
                'start_time' => '15:00',
                'end_time' => '17:00',
                'location' => 'Ruang Musik & Aula',
                'description' => 'Latihan gabungan instrumentalia dan paduan suara.',
                'is_open' => true,
                'created_by' => $admin?->id,
            ]
        );

        // 4. Sesi Mendatang (Upcoming)
        $upcomingSession = AttendanceSession::updateOrCreate(
            ['title' => 'Forum Evaluasi & Rapat Anggota Ekstrakurikuler'],
            [
                'session_type' => AttendanceSessionType::Forum,
                'date' => now()->addDays(5)->toDateString(),
                'start_time' => '14:00',
                'end_time' => '16:00',
                'location' => 'Ruang Multimedia',
                'description' => 'Evaluasi keaktifan anggota dan persiapan jadwal semester depan.',
                'is_open' => true,
                'created_by' => $admin?->id,
            ]
        );

        // Isi presensi untuk sesi-sesi lampau
        $statuses = [
            AttendanceStatus::Present,
            AttendanceStatus::Present,
            AttendanceStatus::Late,
            AttendanceStatus::Excused,
            AttendanceStatus::Present,
        ];

        foreach ([$pastSession1, $pastSession2] as $session) {
            foreach ($members as $index => $member) {
                $status = $statuses[$index % count($statuses)];
                $notes = match ($status) {
                    AttendanceStatus::Late => 'Terlambat 10 menit karena piket kelas',
                    AttendanceStatus::Excused => 'Izin bimbingan belajar',
                    AttendanceStatus::Sick => 'Sakit demam',
                    default => null,
                };

                AttendanceRecord::updateOrCreate(
                    [
                        'attendance_session_id' => $session->id,
                        'user_id' => $member->id,
                    ],
                    [
                        'status' => $status,
                        'notes' => $notes,
                        'recorded_at' => $session->date->copy()->setTime(17, 30),
                        'recorded_by' => $admin?->id,
                    ]
                );
            }
        }
    }
}
