<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ReasonVisit;

class ReasonVisitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ReasonVisit::create([
            'name' => 'Emprendimiento',
            'isGroup' => false,
        ]);

        ReasonVisit::create([
            'name' => 'Proyecto académico',
            'isGroup' => false,
        ]);

        ReasonVisit::create([
            'name' => 'Eventos',
            'isGroup' => false,
        ]);

        ReasonVisit::create([
            'name' => 'Tour/Visita general',
            'isGroup' => true,
        ]);

        ReasonVisit::create([
            'name' => 'Voluntariado',
            'isGroup' => true,
        ]);

        ReasonVisit::create([
            'name' => 'Productos',
            'isGroup' => false,
        ]);
    }
}
