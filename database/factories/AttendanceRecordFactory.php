<?php

namespace Database\Factories;

use App\Enums\AttendanceStatus;
use App\Models\AttendanceSession;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AttendanceRecord>
 */
class AttendanceRecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'attendance_session_id' => AttendanceSession::factory(),
            'user_id' => User::factory()->member(),
            'status' => AttendanceStatus::Present,
            'notes' => null,
            'recorded_at' => now(),
            'recorded_by' => null,
        ];
    }

    /**
     * Indicate that the attendance is late.
     */
    public function late(): static
    {
        return $this->state(fn () => [
            'status' => AttendanceStatus::Late,
            'notes' => 'Terlambat 15 menit',
        ]);
    }

    /**
     * Indicate that the attendance is excused.
     */
    public function excused(): static
    {
        return $this->state(fn () => [
            'status' => AttendanceStatus::Excused,
            'notes' => 'Izin keperluan keluarga',
        ]);
    }

    /**
     * Indicate that the student is sick.
     */
    public function sick(): static
    {
        return $this->state(fn () => [
            'status' => AttendanceStatus::Sick,
            'notes' => 'Surat dokter terlampir',
        ]);
    }

    /**
     * Indicate that the student is absent without notice.
     */
    public function absent(): static
    {
        return $this->state(fn () => [
            'status' => AttendanceStatus::Absent,
            'notes' => null,
        ]);
    }
}
