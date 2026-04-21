<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            InstitutionSeeder::class,
            SubjectTemplateSeeder::class,
        ]);

        $user = User::factory()->create([
            'name' => 'Luciano',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'institution_id' => 1,
            'locale' => 'es',
        ]);

        $user->subjects()->createMany([
            ['name' => 'Matemáticas', 'element' => 'water', 'color' => '#2563eb', 'description' => 'Álgebra, cálculo y análisis matemático'],
            ['name' => 'Programación 1', 'element' => 'fire', 'color' => '#dc2626', 'description' => 'Fundamentos de programación en C'],
            ['name' => 'Arquitectura y Sistemas de Gestión', 'element' => 'earth', 'color' => '#16a34a', 'description' => 'Arquitectura de computadoras y SO'],
            ['name' => 'Organización Empresarial', 'element' => 'air', 'color' => '#ca8a04', 'description' => 'Gestión y administración de organizaciones'],
        ]);
    }
}
