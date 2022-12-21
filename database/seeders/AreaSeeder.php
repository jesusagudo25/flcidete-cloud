<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Area;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Area::create([
            'name' => 'Electrónica',
        ]);

        Area::create([
            'name' => 'Mini Fresadora CNC',
        ]);

        Area::create([
            'name' => 'Láser CNC',
        ]);

        Area::create([
            'name' => 'Cortadora de Vinilo',
        ]);

        Area::create([
            'name' => 'Impresión 3D en filamento',
        ]);

        Area::create([
            'name' => 'Impresión 3D en resina',
        ]);

        Area::create([
            'name' => 'Softwares',
        ]);

        Area::create([
            'name' => 'Bordadora CNC',
        ]);
    }
}
