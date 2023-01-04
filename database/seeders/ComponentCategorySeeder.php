<?php

namespace Database\Seeders;

use App\Models\ComponentCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ComponentCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ComponentCategory::create(
            [
                'name' => 'Microcontroladores',
                'id' => 1,
            ]
        );

        ComponentCategory::create(
            [
                'name' => 'Sensores',
                'id' => 2,
            ]
        );

        ComponentCategory::create(
            [
                'name' => 'Motores',
                'id' => 3,
            ]
        );

        ComponentCategory::create(
            [
                'name' => 'Displays',
                'id' => 4,
            ]
        );

        ComponentCategory::create(
            [
                'name' => 'Baterías',
                'id' => 5,
            ]
        );

        ComponentCategory::create(
            [
                'name' => 'Cables',
                'id' => 6,
            ]
        );

        ComponentCategory::create(
            [
                'name' => 'Conectores',
                'id' => 7,
            ]
        );

        ComponentCategory::create(
            [
                'name' => 'Protoboard',
                'id' => 8,
            ]
        );
        
    }
}
