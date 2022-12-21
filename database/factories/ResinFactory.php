<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Resin>
 */
class ResinFactory extends Factory
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
            'purchase_price' => $this->faker->randomFloat(2, 40, 50),
            'estimated_value' => $this->faker->randomFloat(2, 40, 50),
            'percentage' => $this->faker->randomNumber(2),
            'sale_price' => $this->faker->randomFloat(2, 60, 70),
            'purchased_weight' => $this->faker->numberBetween(1000, 1000),
            'current_weight' => $this->faker->numberBetween(1000, 1000),
        ];
    }
}
