<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TechExpense>
 */
class TechExpenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'area_id' => $this->faker->numberBetween(1, 3),
            'user_id' => 1,
            'name' => $this->faker->name,
            'description' => $this->faker->text,
            'amount' => $this->faker->randomFloat(2, 0, 1000),
        ];
    }
}
