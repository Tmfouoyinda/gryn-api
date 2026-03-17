<?php

namespace Database\Seeders;

use App\Models\Badge;
use App\Models\Challenge;
use Illuminate\Database\Seeder;

class BadgeAndChallengeSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedBadges();
        $this->seedChallenges();
    }

    private function seedBadges(): void
    {
        $badges = [
            [
                'name'            => 'Éco-débutant',
                'icon'            => '🌱',
                'description'     => 'Vous avez effectué votre premier calcul d\'empreinte carbone.',
                'condition_type'  => 'calculations_count',
                'condition_value' => 1,
                'points_reward'   => 50,
            ],
            [
                'name'            => 'Calculateur assidu',
                'icon'            => '📊',
                'description'     => 'Vous avez effectué 5 calculs d\'empreinte carbone.',
                'condition_type'  => 'calculations_count',
                'condition_value' => 5,
                'points_reward'   => 100,
            ],
            [
                'name'            => 'Champion du climat',
                'icon'            => '🏆',
                'description'     => 'Vous avez effectué 20 calculs d\'empreinte carbone.',
                'condition_type'  => 'calculations_count',
                'condition_value' => 20,
                'points_reward'   => 300,
            ],
            [
                'name'            => 'Premier défi',
                'icon'            => '⚡',
                'description'     => 'Vous avez complété votre premier challenge écologique.',
                'condition_type'  => 'challenge_completed',
                'condition_value' => 1,
                'points_reward'   => 150,
            ],
            [
                'name'            => 'Éco-warrior',
                'icon'            => '🛡️',
                'description'     => 'Vous avez complété 5 challenges écologiques.',
                'condition_type'  => 'challenge_completed',
                'condition_value' => 5,
                'points_reward'   => 400,
            ],
            [
                'name'            => 'Légende verte',
                'icon'            => '👑',
                'description'     => 'Vous avez complété 10 challenges écologiques.',
                'condition_type'  => 'challenge_completed',
                'condition_value' => 10,
                'points_reward'   => 1000,
            ],
        ];

        foreach ($badges as $data) {
            Badge::firstOrCreate(['name' => $data['name']], $data);
        }
    }

    private function seedChallenges(): void
    {
        $premierDefi = Badge::where('name', 'Premier défi')->first();
        $ecoWarrior  = Badge::where('name', 'Éco-warrior')->first();

        $challenges = [
            [
                'title'            => '30 jours sans voiture',
                'description'      => 'Utilisez uniquement les transports en commun, le vélo ou la marche pendant 30 jours.',
                'duration_days'    => 30,
                'difficulty'       => 'Moyen',
                'co2_reduction_kg' => 150.00,
                'points_reward'    => 200,
                'badge_id'         => $premierDefi?->id,
                'is_active'        => true,
            ],
            [
                'title'            => 'Semaine végétarienne',
                'description'      => 'Adoptez une alimentation 100% végétarienne pendant 7 jours.',
                'duration_days'    => 7,
                'difficulty'       => 'Facile',
                'co2_reduction_kg' => 45.00,
                'points_reward'    => 100,
                'badge_id'         => null,
                'is_active'        => true,
            ],
            [
                'title'            => 'Zéro plastique',
                'description'      => 'Évitez tout emballage plastique à usage unique pendant un mois.',
                'duration_days'    => 30,
                'difficulty'       => 'Difficile',
                'co2_reduction_kg' => 80.00,
                'points_reward'    => 300,
                'badge_id'         => $ecoWarrior?->id,
                'is_active'        => true,
            ],
            [
                'title'            => 'Douches courtes',
                'description'      => 'Limitez vos douches à 5 minutes maximum pendant 14 jours.',
                'duration_days'    => 14,
                'difficulty'       => 'Facile',
                'co2_reduction_kg' => 20.00,
                'points_reward'    => 80,
                'badge_id'         => null,
                'is_active'        => true,
            ],
            [
                'title'            => 'Alimentation locale',
                'description'      => 'Achetez exclusivement des produits locaux et de saison pendant 2 semaines.',
                'duration_days'    => 14,
                'difficulty'       => 'Moyen',
                'co2_reduction_kg' => 35.00,
                'points_reward'    => 150,
                'badge_id'         => null,
                'is_active'        => true,
            ],
            [
                'title'            => 'Digital detox énergétique',
                'description'      => 'Éteignez tous vos appareils en veille et débranchez les chargeurs inutilisés pendant 30 jours.',
                'duration_days'    => 30,
                'difficulty'       => 'Facile',
                'co2_reduction_kg' => 15.00,
                'points_reward'    => 75,
                'badge_id'         => null,
                'is_active'        => true,
            ],
        ];

        foreach ($challenges as $data) {
            Challenge::firstOrCreate(['title' => $data['title']], $data);
        }
    }
}
