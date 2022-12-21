<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'document_type' => $this->faker->randomElements(['C', 'R', 'P']),
            'document_number' => $this->faker->unique()->randomNumber(8, true),
            'name' => $this->faker->name(),
            'age_range_id' => $this->faker->randomElements([1, 2, 3 , 4]),
            'type_sex_id' => $this->faker->randomElements([1, 2, 3 , 4]),
            'email' => $this->faker->unique()->safeEmail,
            'telephone' => $this->faker->unique()->randomNumber(8,true),
            'province_id' => $this->faker->numberBetween(1,2),
            'district_id' => $this->faker->boolean(1,2),
            'township_id' => $this->faker->boolean(1,2)
        ];
    }
}
