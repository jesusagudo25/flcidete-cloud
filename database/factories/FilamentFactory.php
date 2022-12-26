<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Filament>
 */
class FilamentFactory extends Factory
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
            'purchase_price' => $this->faker->randomFloat(2, 30, 40),
            'estimated_value' => $this->faker->randomFloat(2, 30, 40),
            'percentage' => $this->faker->randomNumber(2),
            'sale_price' => $this->faker->randomFloat(2, 45, 50),
            'purchased_weight' => $this->faker->numberBetween(1000, 1000),
            'current_weight' => $this->faker->numberBetween(1000, 1000),
        ];
    }
}
