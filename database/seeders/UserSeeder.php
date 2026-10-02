<?php

namespace Database\Seeders;

use App\Enums\SkillLevel;
use App\Enums\UserRole;
use App\Models\MusicRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vocalist = MusicRole::where('slug', 'vocalist')->first();
        $guitarist = MusicRole::where('slug', 'guitarist')->first();
        $allMusicRoles = MusicRole::all();

        // 1. First Admin
        $admin1 = User::updateOrCreate(
            ['email' => 'admin@example.test'],
            [
                'name' => 'Head Administrator',
                'nis' => 'ADM-001',
                'class_name' => 'Faculty / Staff',
                'phone' => '081234567890',
                'is_choir' => false,
                'is_active' => true,
                'joined_at' => now()->subYears(2)->toDateString(),
                'password' => Hash::make('Password123'),
            ]
        );
        $admin1->role = UserRole::Admin;
        $admin1->save();

        // 2. Second Admin
        $admin2 = User::updateOrCreate(
            ['email' => 'admin2@example.test'],
            [
                'name' => 'Assistant Administrator',
                'nis' => 'ADM-002',
                'class_name' => 'Faculty / Staff',
                'phone' => '081234567891',
                'is_choir' => false,
                'is_active' => true,
                'joined_at' => now()->subYear()->toDateString(),
                'password' => Hash::make('Password123'),
            ]
        );
        $admin2->role = UserRole::Admin;
        $admin2->save();

        // 3. Featured Member (member@example.test)
        $member = User::updateOrCreate(
            ['email' => 'member@example.test'],
            [
                'name' => 'Lead Student Member',
                'nis' => 'STU-2024-001',
                'class_name' => 'XI-IPA-1',
                'phone' => '081234567892',
                'is_choir' => true,
                'is_active' => true,
                'joined_at' => now()->subMonths(6)->toDateString(),
                'password' => Hash::make('Password123'),
            ]
        );
        $member->role = UserRole::Member;
        $member->save();

        if ($vocalist && $guitarist) {
            $member->musicRoles()->syncWithoutDetaching([
                $vocalist->id => [
                    'is_primary' => true,
                    'skill_level' => SkillLevel::Advanced->value,
                ],
                $guitarist->id => [
                    'is_primary' => false,
                    'skill_level' => SkillLevel::Intermediate->value,
                ],
            ]);
        }

        // 4. Extra 15 members with mixed choir/active status and 1–3 talents (exactly one primary)
        $skillLevels = [SkillLevel::Beginner->value, SkillLevel::Intermediate->value, SkillLevel::Advanced->value];

        for ($i = 1; $i <= 15; $i++) {
            $email = "student{$i}@example.test";
            $isChoir = ($i % 3 === 0);
            $isActive = ($i !== 13); // One inactive member for realistic test data

            $extraMember = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => "Student Musician {$i}",
                    'nis' => sprintf('STU-2024-%03d', $i + 1),
                    'class_name' => ($i % 2 === 0) ? 'XI-IPS-2' : 'X-A',
                    'phone' => sprintf('08123456%04d', $i),
                    'is_choir' => $isChoir,
                    'is_active' => $isActive,
                    'joined_at' => now()->subMonths($i)->toDateString(),
                    'password' => Hash::make('Password123'),
                ]
            );
            $extraMember->role = UserRole::Member;
            $extraMember->save();

            if ($allMusicRoles->isNotEmpty()) {
                // Pick 1 to 3 distinct roles
                $count = min(($i % 3) + 1, $allMusicRoles->count());
                $assignedRoles = $allMusicRoles->shuffle()->take($count);

                $pivotData = [];
                $isFirst = true;

                foreach ($assignedRoles as $assignedRole) {
                    $pivotData[$assignedRole->id] = [
                        'is_primary' => $isFirst,
                        'skill_level' => $skillLevels[array_rand($skillLevels)],
                    ];
                    $isFirst = false;
                }

                $extraMember->musicRoles()->sync($pivotData);
            }
        }
    }
}
