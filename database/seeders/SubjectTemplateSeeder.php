<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubjectTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            ['name' => 'Matemáticas', 'element' => 'water', 'color' => '#2563eb'],
            ['name' => 'Programación 1', 'element' => 'fire', 'color' => '#dc2626'],
            ['name' => 'Arquitectura y Sistemas de Gestión', 'element' => 'earth', 'color' => '#16a34a'],
            ['name' => 'Organización Empresarial', 'element' => 'air', 'color' => '#ca8a04'],
            ['name' => 'Inglés 1', 'element' => 'lotus', 'color' => '#8b5cf6'],
            ['name' => 'Metodología de Estudio', 'element' => 'lotus', 'color' => '#6b7280'],
        ];

        foreach ($templates as $template) {
            DB::table('subject_templates')->updateOrInsert(
                ['name' => $template['name']],
                $template,
            );
        }
    }
}
