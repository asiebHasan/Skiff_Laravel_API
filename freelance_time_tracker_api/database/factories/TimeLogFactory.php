<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Project;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TimeLog>
 */
class TimeLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Pick a random date
        $date = $this->faker->dateTimeBetween('-2 weeks', 'now');

        // Generate start time between 6 AM and 2 PM
        $start = (clone $date)->setTime(rand(6, 14), rand(0, 59));

        // Add 1 to 8 hours to start time
        $end = (clone $start)->modify('+' . rand(1, 8) . ' hours');

        // Calculate duration in hours
        $hours = ($end->getTimestamp() - $start->getTimestamp()) / 3600;

        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'project_id' => Project::inRandomOrder()->first()->id,
            'description' => $this->faker->sentence,
            'start_time' => $start,
            'end_time' => $end,
            'hours' => round($hours, 2),
            'tag' => $this->faker->randomElement(['billable', 'non-billable']),
        ];
    }
}
