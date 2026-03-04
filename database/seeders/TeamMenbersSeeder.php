<?php

namespace Database\Seeders;

use App\Models\TeamMember;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TeamMenbersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

     
    public function run(): void
    {
        TeamMember::insert([
            [
                'id' => 1,
                'name' => `Teddy M'FOUO`,
                'role' => 'Responsable des pages Accueil et À propos, ainsi que des pages Mot de passe oublié et 
                Réinitialisation du mot de passe.',
            ],

            [
                'id' => 2,
                'name' => 'Modibo Toure',
                'role' => 'Responsable des pages profil utilisation et connexion, 
                ainsi que des pages creation de compte. ',

            ],

            [
                'id' => 3,
                'name' => 'Lucas OKINDA',
                'role' => 'Responsable des pages calculateur.',
            ],

            [
                'id' => 4,
                'name' => 'Luca PISSELI',
                'role' => 'Responsable des pages challenges.',
            ],
            ]);

    }
}
