<?php

namespace Database\Seeders;

use App\Models\Component;
use App\Models\ComponentUpdate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ComponentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Component::create(
            [
                'name' => 'Arduino Uno',
                'component_category_id' => 1,
                'id' => 1,
                'estimated_value' => 25,
                'purchase_price' => 25,
                'percentage' => 25,
                'sale_price' => 31.25,
                'stock' => 10,
            ],
        );

        ComponentUpdate::create(
            [
                'component_id' => 1,
                'id' => 1,
                'purchase_price' => 25,
                'estimated_value' => 25,
                'percentage' => 25,
                'sale_price' => 31.25,
                'quantity' => 10,
            ],
        );

    }
}
