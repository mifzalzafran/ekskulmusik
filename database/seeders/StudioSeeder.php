<?php

namespace Database\Seeders;

use App\Enums\StudioBookingStatus;
use App\Models\StudioBooking;
use App\Models\StudioRoom;
use App\Models\User;
use Illuminate\Database\Seeder;

class StudioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $room1 = StudioRoom::firstOrCreate(
            ['name' => 'Studio Utama (Band & Acoustic)'],
            [
                'capacity' => 12,
                'description' => 'Ruang kedap suara utama dilengkapi drum kit akustik, amplifier gitar/bass, keyboard elektrik, dan sound monitor.',
                'is_active' => true,
            ]
        );

        $room2 = StudioRoom::firstOrCreate(
            ['name' => 'Studio Vokal & Sectional Padus'],
            [
                'capacity' => 20,
                'description' => 'Ruang latihan vokal dan latihan pecahan suara paduan suara (Sopran, Alto, Tenor, Bass) dengan piano upright dan cermin vokal.',
                'is_active' => true,
            ]
        );

        $student = User::where('role', 'member')->first();
        $admin = User::where('role', 'admin')->first();

        if ($student && $admin) {
            // Sample approved booking for today
            StudioBooking::firstOrCreate(
                [
                    'studio_room_id' => $room1->id,
                    'booking_date' => now()->toDateString(),
                    'start_time' => '15:30:00',
                ],
                [
                    'user_id' => $student->id,
                    'end_time' => '17:00:00',
                    'purpose' => 'Latihan Band Persiapan Pentas Seni SAF',
                    'status' => StudioBookingStatus::Approved,
                    'approved_by' => $admin->id,
                    'notes' => 'Gunakan sandal studio dan jangan bawa makanan berkuah.',
                ]
            );

            // Sample pending booking for tomorrow
            StudioBooking::firstOrCreate(
                [
                    'studio_room_id' => $room2->id,
                    'booking_date' => now()->addDay()->toDateString(),
                    'start_time' => '14:00:00',
                ],
                [
                    'user_id' => $student->id,
                    'end_time' => '15:30:00',
                    'purpose' => 'Latihan Vokal Solo & Duet Upacara',
                    'status' => StudioBookingStatus::Pending,
                ]
            );
        }
    }
}
