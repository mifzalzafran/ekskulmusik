<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            MusicRoleSeeder::class,
            UserSeeder::class,
            AttendanceSeeder::class,
            StudioSeeder::class,
            InstrumentSeeder::class,
            CashSeeder::class,
            EventSeeder::class,
        ]);
    }
}
