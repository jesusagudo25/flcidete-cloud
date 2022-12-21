<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Component>
 */
class ComponentFactory extends Factory
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
            'estimated_value' => $this->faker->randomFloat(2, 1, 20),
            'percentage' => $this->faker->randomNumber(2),
            'quantity' => 5,
            'sale_price' => $this->faker->randomFloat(2, 25, 30),
            'component_category_id' => $this->faker->numberBetween(1, 10),
            'stock' => 5,
        ];
    }
}
