<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Utilisateur de test
       /* User::factory()->create([
            'name'       => 'Teddy Fom',
            'first_name' => 'Teddy',
            'last_name'  => 'Fom',
            'email'      => 'teddy@example.com',
        ]);*/

        $this->call(BadgeAndChallengeSeeder::class);
    }
}
