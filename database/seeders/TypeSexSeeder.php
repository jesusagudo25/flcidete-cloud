<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TypeSex;

class TypeSexSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TypeSex::create([
            'id' => 1,
            'name' => 'Masculino',
        ]);

        TypeSex::create([
            'id' => 2,
            'name' => 'Femenino',
        ]);
    }
}
