<?php

namespace Database\Factories;

use App\Enums\AttendanceSessionType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AttendanceSession>
 */
class AttendanceSessionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->randomElement([
                'Latihan Rutin Band & Vokal',
                'Gladi Bersih Aubade Upacara Bendera',
                'Forum Bulanan & Evaluasi Departemen Musik',
                'Latihan Persiapan Festival Seni',
                'Workshop Dasar Aransemen Musik',
            ]),
            'session_type' => fake()->randomElement(AttendanceSessionType::cases()),
            'date' => fake()->dateTimeBetween('-1 month', '+1 month')->format('Y-m-d'),
            'start_time' => '15:30',
            'end_time' => '17:30',
            'location' => fake()->randomElement(['Ruang Studio Musik', 'Aula Sekolah', 'Lapangan Utama']),
            'description' => fake()->sentence(),
            'is_open' => true,
            'created_by' => null,
        ];
    }

    /**
     * Indicate that the session is for regular practice.
     */
    public function latihan(): static
    {
        return $this->state(fn () => [
            'session_type' => AttendanceSessionType::Latihan,
            'title' => 'Latihan Rutin Ekstrakurikuler',
        ]);
    }

    /**
     * Indicate that the session is for flag ceremony / upacara.
     */
    public function upacara(): static
    {
        return $this->state(fn () => [
            'session_type' => AttendanceSessionType::Upacara,
            'title' => 'Penugasan Musik Upacara Bendera',
        ]);
    }

    /**
     * Indicate that the session is for general forum.
     */
    public function forum(): static
    {
        return $this->state(fn () => [
            'session_type' => AttendanceSessionType::Forum,
            'title' => 'Forum Koordinasi & Rapat Anggota',
        ]);
    }

    /**
     * Indicate that the session attendance is closed.
     */
    public function closed(): static
    {
        return $this->state(fn () => [
            'is_open' => false,
        ]);
    }
}
