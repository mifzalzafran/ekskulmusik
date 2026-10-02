<?php

namespace Database\Seeders;

use App\Models\MusicRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MusicRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            'Vocalist' => 'Lead singer providing main melodic lines.',
            'Backing Vocalist' => 'Harmonic and background vocal support.',
            'Guitarist' => 'Acoustic or electric guitar player.',
            'Bassist' => 'Bass guitar and low-end groove anchor.',
            'Drummer' => 'Drum kit and rhythmic foundation.',
            'Keyboardist' => 'Synthesizers, organs, and digital keyboards.',
            'Pianist' => 'Acoustic grand/upright piano performer.',
            'Violinist' => 'Bowed string instrument performer.',
            'Percussionist' => 'Hand percussion, cymbals, and auxiliary rhythm.',
            'Brass/Wind Player' => 'Trumpet, saxophone, flute, and other wind instruments.',
            'Sound Engineer' => 'Live sound mixing, audio routing, and acoustics.',
            'Conductor' => 'Ensemble director and tempo coordination.',
            'Other' => 'Miscellaneous instruments and musical roles.',
        ];

        foreach ($roles as $name => $description) {
            MusicRole::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'description' => $description,
                    'is_active' => true,
                ]
            );
        }
    }
}
