<?php

namespace Database\Seeders;

use App\Enums\EventStatus;
use App\Enums\EventType;
use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\User;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $members = User::where('role', 'member')->get();

        // 1. Upcoming SAF (Pensi)
        $eventPensi = Event::firstOrCreate(
            ['title' => 'Pentas Seni SAF (Sound of Art & Fest) 2026'],
            [
                'event_type' => EventType::PensiSaf,
                'start_date' => now()->addDays(20)->toDateString(),
                'end_date' => now()->addDays(20)->toDateString(),
                'start_time' => '09:00:00',
                'location' => 'Panggung Utama Lapangan SMA',
                'description' => 'Penampilan puncak tahunan seluruh unit ekskul musik: Solo Vokal, Akustik Ensemble, Band Festival, dan Kolaborasi Paduan Suara.',
                'status' => EventStatus::Upcoming,
            ]
        );

        // 2. Upcoming Choir competition
        $eventLomba = Event::firstOrCreate(
            ['title' => 'Festival Paduan Suara Pelajar SMA Tingkat Kota'],
            [
                'event_type' => EventType::Competition,
                'start_date' => now()->addDays(40)->toDateString(),
                'end_date' => now()->addDays(41)->toDateString(),
                'start_time' => '08:00:00',
                'location' => 'Auditorium Gedung Kesenian Daerah',
                'description' => 'Kompetisi paduan suara membawakan lagu wajib nasional dan satu lagu daerah Nusantara.',
                'status' => EventStatus::Upcoming,
            ]
        );

        // 3. Demo Ekskul
        Event::firstOrCreate(
            ['title' => 'Demo Ekskul Musik untuk Siswa Baru Tahun Ajaran Baru'],
            [
                'event_type' => EventType::DemoEskul,
                'start_date' => now()->addMonths(2)->toDateString(),
                'start_time' => '13:00:00',
                'location' => 'Aula Serbaguna Sekolah',
                'description' => 'Unjuk kebolehan instrumen dan pengenalan divisi musik serta paduan suara untuk perekrutan adik kelas.',
                'status' => EventStatus::Upcoming,
            ]
        );

        // Assign some participants
        if ($members->isNotEmpty()) {
            foreach ($members->take(6) as $index => $member) {
                EventParticipant::firstOrCreate(
                    [
                        'event_id' => $eventPensi->id,
                        'user_id' => $member->id,
                    ],
                    [
                        'role_in_event' => ($index % 2 === 0) ? 'Band Performer' : 'Paduan Suara Collab',
                        'notes' => 'Lagu: Bendera (Cokelat) & Rumah Kita',
                    ]
                );
            }
        }
    }
}
