<?php

namespace Database\Seeders;

use App\Enums\CashTransactionType;
use App\Models\CashTransaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class CashSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $member = User::where('role', 'member')->first();

        if (! $admin) {
            return;
        }

        // 1. Initial cash balance / school support
        CashTransaction::firstOrCreate(
            ['description' => 'Subsidi Dana Operasional Eskul Musik dari Sekolah'],
            [
                'type' => CashTransactionType::Income,
                'category' => 'Subsidi Sekolah',
                'amount' => 1500000.00,
                'transaction_date' => now()->startOfMonth()->toDateString(),
                'recorded_by' => $admin->id,
            ]
        );

        // 2. Member monthly dues
        if ($member) {
            CashTransaction::firstOrCreate(
                ['description' => 'Iuran Kas Bulanan Anggota (Bulan Ini) - ' . $member->name],
                [
                    'type' => CashTransactionType::Income,
                    'category' => 'Iuran Kas Anggota',
                    'amount' => 20000.00,
                    'transaction_date' => now()->subDays(5)->toDateString(),
                    'user_id' => $member->id,
                    'recorded_by' => $admin->id,
                ]
            );
        }

        // 3. Maintenance expense
        CashTransaction::firstOrCreate(
            ['description' => 'Pembelian 2 Set Senar Gitar Akustik D-Addario & Stik Drum Zildjian 5A'],
            [
                'type' => CashTransactionType::Expense,
                'category' => 'Pemeliharaan Alat',
                'amount' => 245000.00,
                'transaction_date' => now()->subDays(3)->toDateString(),
                'recorded_by' => $admin->id,
            ]
        );

        // 4. Rehearsal refreshment
        CashTransaction::firstOrCreate(
            ['description' => 'Konsumsi Air Mineral & Snack Latihan Gabungan Paduan Suara'],
            [
                'type' => CashTransactionType::Expense,
                'category' => 'Konsumsi Latihan',
                'amount' => 75000.00,
                'transaction_date' => now()->subDay()->toDateString(),
                'recorded_by' => $admin->id,
            ]
        );
    }
}
