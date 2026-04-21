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

        User::factory()->create([
            'name' => 'Luciano',
            'email' => 'test@example.com',
            'institution_id' => 1,
            'locale' => 'es',
        ]);
    }
}
