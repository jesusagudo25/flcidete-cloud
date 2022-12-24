<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Stabilizer>
 */
class StabilizerFactory extends Factory
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
            'width' => $this->faker->numberBetween(1, 100),
            'width_base' => $this->faker->numberBetween(1, 100),
            'height' => $this->faker->numberBetween(1, 100),
            'height_base' => $this->faker->numberBetween(1, 100),
            'height_in_yd' => $this->faker->randomNumber(2),
            'area' => $this->faker->numberBetween(1, 100),
            'area_base' => $this->faker->numberBetween(1, 100),
            'purchase_price' => $this->faker->randomFloat(2, 40, 50),
            'purchase_price_base' => $this->faker->randomFloat(2, 40, 50),
            'estimated_value' => $this->faker->randomFloat(2, 40, 50),
            'estimated_value_base' => $this->faker->randomFloat(2, 40, 50),
        ];
    }
}
