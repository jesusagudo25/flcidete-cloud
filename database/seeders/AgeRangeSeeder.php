<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AgeRange;

class AgeRangeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AgeRange::create([
            'name' => '18 o menos',
        ]);

        AgeRange::create([
            'name' => '19 - 26',
        ]);

        AgeRange::create([
            'name' => '27 - 35',
        ]);

        AgeRange::create([
            'name' => '36 - más',
        ]);
    }
}
