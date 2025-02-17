<?php

namespace Database\Factories;

use App\Models\Priority;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'priority_id' => Priority::factory(),
            'calendar_id' => 1,
            'title' => $this->faker->sentence,
            'start_time' => $this->faker->dateTime,
            'end_time' => $this->faker->dateTime,
            'reservation_time' => $this->faker->dateTime,
            'status' => 1,
            'url' => $this->faker->url,
            'detail' => $this->faker->sentence,
        ];
    }
}
