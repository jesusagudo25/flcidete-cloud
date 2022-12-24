<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Software>
 */
class SoftwareFactory extends Factory
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
            'purchase_price' => $this->faker->randomFloat(2, 1, 20),
            'purchase_price_base' => $this->faker->randomFloat(2, 1, 20),
            'estimated_value' => $this->faker->randomFloat(2, 1, 20),
            'estimated_value_base' => $this->faker->randomFloat(2, 1, 20),
            'sale_price' => $this->faker->randomFloat(2, 25, 30),
            'sale_price_base' => $this->faker->randomFloat(2, 25, 30),
            'purchased_date' => $this->faker->dateTimeBetween('-1 years', 'now'),
            'purchased_date_base' => $this->faker->dateTimeBetween('-1 years', 'now'),
            'expiration_date' => $this->faker->dateTimeBetween('now', '+1 years'),
            'expiration_date_base' => $this->faker->dateTimeBetween('now', '+1 years'),
        ];
    }
}
