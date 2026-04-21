<?php

namespace Database\Seeders;

use App\Models\Institution;
use Illuminate\Database\Seeder;

class InstitutionSeeder extends Seeder
{
    public function run(): void
    {
        Institution::updateOrCreate(
            ['short_name' => 'UTN FRRe'],
            [
                'name' => 'Universidad Tecnológica Nacional - Facultad Regional Resistencia',
                'moodle_url' => 'https://www.cvfrre.com.ar',
                'website' => 'https://www.frre.utn.edu.ar',
                'color' => '#003366',
            ],
        );
    }
}
