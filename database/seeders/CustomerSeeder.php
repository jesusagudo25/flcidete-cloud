<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Customer::create(
            [
                'name' => 'Juan',
                'id' => 1,
                'document_type' => 'C',
                'document_number' => '9-121-8283',
                'email' => Str::random(10) . '@gmail.com',
                'telephone' => '12345678',
                'province_id' => 1,
                'district_id' => 1,
                'township_id' => 1,
                'age_range_id' => 1,
                'type_sex_id' => 1,
            ],
        );
    }
}
