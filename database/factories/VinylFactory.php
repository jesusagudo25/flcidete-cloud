<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vinyl>
 */
class VinylFactory extends Factory
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
            'cost' => $this->faker->randomFloat(2, 1, 20),
            'cost_base' => $this->faker->randomFloat(2, 1, 20),
            'purchase_price' => $this->faker->randomFloat(3, 0.004, 0.008),
            'purchase_price_base' => $this->faker->randomFloat(3, 0.004, 0.008),
            'estimated_value' => $this->faker->randomFloat(3, 0.004, 0.008),
            'estimated_value_base' => $this->faker->randomFloat(3, 0.004, 0.008),
            'percentage' => $this->faker->randomNumber(2),
            'percentage_base' => $this->faker->randomNumber(2),
            'sale_price' => $this->faker->randomFloat(3, 0.010, 0.020),
            'sale_price_base' => $this->faker->randomFloat(3, 0.010, 0.020),
            'width' => $this->faker->numberBetween(24, 24),
            'width_base' => $this->faker->numberBetween(24, 24),
            'height' => $this->faker->numberBetween(1200, 1200),
            'height_base' => $this->faker->numberBetween(1200, 1200),
            'height_in_feet' => $this->faker->numberBetween(100, 100),
            'area' => $this->faker->numberBetween(28800, 28800),
            'area_base' => $this->faker->numberBetween(28800, 28800),
        ];
    }
}
