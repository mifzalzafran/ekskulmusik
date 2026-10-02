<?php

namespace Database\Factories;

use App\Models\MusicRole;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<MusicRole>
 */
class MusicRoleFactory extends Factory
{
    protected $model = MusicRole::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'Vocalist',
            'Backing Vocalist',
            'Guitarist',
            'Bassist',
            'Drummer',
            'Keyboardist',
            'Pianist',
            'Violinist',
            'Percussionist',
            'Brass/Wind Player',
            'Sound Engineer',
            'Conductor',
            'Other',
        ]) . ' ' . fake()->unique()->randomNumber(3);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the music role is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
