<?php

namespace Database\Seeders;

use App\Enums\InstrumentOwnership;
use App\Enums\InstrumentStatus;
use App\Models\Instrument;
use App\Models\InstrumentLoan;
use App\Models\User;
use Illuminate\Database\Seeder;

class InstrumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $members = User::where('role', 'member')->take(5)->get();
        $admin = User::where('role', 'admin')->first();

        $leadStudent = $members->first();
        $otherStudent = $members->skip(1)->first();

        // 1. School owned instruments
        $drum = Instrument::firstOrCreate(
            ['name' => 'Drum Kit Pearl Export Series (Smokey Chrome)'],
            [
                'category' => 'Percussion',
                'brand_model' => 'Pearl Export EXX725SP',
                'serial_number' => 'PRL-DRUM-2024-01',
                'ownership_type' => InstrumentOwnership::School,
                'status' => InstrumentStatus::Available,
                'location_slot' => 'Stage Drum Riser',
                'condition_notes' => 'Kondisi mulus, skin snare baru diganti Remo Ambassador.',
            ]
        );

        $keyboard = Instrument::firstOrCreate(
            ['name' => 'Synthesizer Workstation Yamaha MX61'],
            [
                'category' => 'Keyboard',
                'brand_model' => 'Yamaha MX61 Black',
                'serial_number' => 'YMH-KEY-9941',
                'ownership_type' => InstrumentOwnership::School,
                'status' => InstrumentStatus::Available,
                'location_slot' => 'Keyboard Stand 1',
                'condition_notes' => 'Adaptor original dan sustain pedal tersedia.',
            ]
        );

        $fenderAmp = Instrument::firstOrCreate(
            ['name' => 'Amplifier Gitar Fender Champion 100W'],
            [
                'category' => 'Audio Gear',
                'brand_model' => 'Fender Champion 100',
                'serial_number' => 'FND-AMP-5521',
                'ownership_type' => InstrumentOwnership::School,
                'status' => InstrumentStatus::Available,
                'location_slot' => 'Rak Amp 1',
                'condition_notes' => 'Kabel power dan footswitch lengkap.',
            ]
        );

        $micSet = Instrument::firstOrCreate(
            ['name' => 'Set Mikrofon Shure SM58 (Isi 4)'],
            [
                'category' => 'Vocal / Audio Gear',
                'brand_model' => 'Shure SM58-LC',
                'serial_number' => 'SHR-MIC-SET4',
                'ownership_type' => InstrumentOwnership::School,
                'status' => InstrumentStatus::InUse,
                'location_slot' => 'Lemari Mic Hardcase B',
                'condition_notes' => 'Sedang dipakai latihan vokal grup.',
            ]
        );

        // 2. Student deposited instruments
        if ($leadStudent) {
            $guitarStudent = Instrument::firstOrCreate(
                ['name' => 'Gitar Akustik Yamaha F310 Natural'],
                [
                    'category' => 'Strings',
                    'brand_model' => 'Yamaha F310',
                    'serial_number' => 'STU-GTR-001',
                    'ownership_type' => InstrumentOwnership::Student,
                    'owner_user_id' => $leadStudent->id,
                    'status' => InstrumentStatus::Available,
                    'location_slot' => 'Gantungan Dinding A-01',
                    'condition_notes' => 'Boleh dipinjam sesama anggota eskul di studio. Mohon lap setelah pakai.',
                ]
            );

            $bassStudent = Instrument::firstOrCreate(
                ['name' => 'Bass Elektrik Ibanez Gio GSR200 (Hitam)'],
                [
                    'category' => 'Strings',
                    'brand_model' => 'Ibanez GSR200-BK',
                    'serial_number' => 'STU-BSS-002',
                    'ownership_type' => InstrumentOwnership::Student,
                    'owner_user_id' => $leadStudent->id,
                    'status' => InstrumentStatus::InUse,
                    'location_slot' => 'Gantungan Dinding B-03',
                    'condition_notes' => 'Senar D-Addario ProSteels baru.',
                ]
            );

            // Record loan for the bass (borrowed by another student)
            if ($otherStudent && $admin) {
                InstrumentLoan::firstOrCreate(
                    [
                        'instrument_id' => $bassStudent->id,
                        'borrower_user_id' => $otherStudent->id,
                        'returned_at' => null,
                    ],
                    [
                        'borrowed_at' => now()->subHours(2),
                        'condition_before' => 'Kondisi baik, mulus',
                        'handled_by' => $admin->id,
                        'notes' => 'Dipinjam untuk latihan lagu Upacara',
                    ]
                );
            }
        }

        if ($otherStudent) {
            Instrument::firstOrCreate(
                ['name' => 'Biola Akustik Stentor Student II 4/4'],
                [
                    'category' => 'Strings',
                    'brand_model' => 'Stentor Student II',
                    'serial_number' => 'STU-VLN-003',
                    'ownership_type' => InstrumentOwnership::Student,
                    'owner_user_id' => $otherStudent->id,
                    'status' => InstrumentStatus::TakenHome,
                    'location_slot' => 'Loker 3 (Kosong)',
                    'condition_notes' => 'Sedang dibawa pulang oleh pemilik untuk latihan privat di rumah.',
                ]
            );
        }
    }
}
