<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Client;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title =$this->faker->words(5, true);
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'title' => $title,
            'description' => $this->faker->sentence,
            'client_id' => Client::inRandomOrder()->first()->id,
            'status' => 'active',
            'deadline' => $this->faker->dateTime
        ];
    }
}