<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MaterialLaser>
 */
class MaterialLaserFactory extends Factory
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
            'cost' => $this->faker->randomFloat(2, 10.00, 15.00),
            'purchase_price' => $this->faker->randomFloat(2, 3.12, 3.75),
            'estimated_value' => $this->faker->randomFloat(2, 3.12, 3.75),
            'percentage' => $this->faker->randomNumber(2),
            'sale_price' => $this->faker->randomFloat(2, 4.00, 5.00),
            'width' => $this->faker->numberBetween(4, 4),
            'height' => $this->faker->numberBetween(8, 8),
            'area' => $this->faker->numberBetween(32, 32),
        ];
    }
}
