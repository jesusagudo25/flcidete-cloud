<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Thread>
 */
class ThreadFactory extends Factory
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
            'price_purchase' => $this->faker->randomFloat(2, 1, 20),
            'price_purchase_base' => $this->faker->randomFloat(2, 1, 20),
            'estimated_value' => $this->faker->randomFloat(2, 1, 20),
            'estimated_value_base' => $this->faker->randomFloat(2, 1, 20),
            'purchased_amount' => 5000,
            'purchased_amount_base' => 5000,
        ];
    }
}
