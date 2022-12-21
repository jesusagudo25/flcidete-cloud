<?php

namespace Database\Factories;

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
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'event_category_id' => $this->faker->numberBetween(1, 3),
            'initial_date' => $this->faker->dateTimeBetween('-1 years', 'now'),
            'final_date' => $this->faker->dateTimeBetween('now', '+1 years'),
            'initial_time' => $this->faker->time('H:i:s', 'now'),
            'final_time' => $this->faker->time('H:i:s', 'now'),
            'max_participants' => 12,
            'quotas' => 12,
            'price' => $this->faker->randomFloat(2, 1, 20),
            'expenses' => $this->faker->randomFloat(2, 1, 20),
            'description_expenses' => $this->faker->text,
        ];
    }
}
