<?php

namespace Database\Seeders;

use App\Models\Badge;
use App\Models\Challenge;
use Illuminate\Database\Seeder;

class ChallengeAndBadgeSeeder extends Seeder
{
    public function run(): void
    {
        // -----------------------------------------------
        // Challenges
        // -----------------------------------------------
        $challenges = [
            [
                'slug'              => '30-jours-sans-voiture',
                'titre'             => '30 jours sans voiture',
                'description'       => 'Utilisez uniquement les transports en commun, le vélo ou la marche pendant 30 jours.',
                'difficulte'        => 'Moyen',
                'duree'             => '30 jours',
                'duree_jours'       => 30,
                'reduction_co2'     => '150 kg CO₂',
                'reduction_co2_kg'  => 150,
                'points_recompense' => 200,
                'actif'             => true,
            ],
            [
                'slug'              => 'semaine-vegetarienne',
                'titre'             => 'Semaine végétarienne',
                'description'       => 'Adoptez une alimentation 100% végétarienne pendant 7 jours.',
                'difficulte'        => 'Facile',
                'duree'             => '7 jours',
                'duree_jours'       => 7,
                'reduction_co2'     => '45 kg CO₂',
                'reduction_co2_kg'  => 45,
                'points_recompense' => 75,
                'actif'             => true,
            ],
            [
                'slug'              => 'zero-dechet-plastique',
                'titre'             => 'Zéro déchet plastique',
                'description'       => 'Évitez tout emballage plastique à usage unique pendant un mois.',
                'difficulte'        => 'Difficile',
                'duree'             => '30 jours',
                'duree_jours'       => 30,
                'reduction_co2'     => '80 kg CO₂',
                'reduction_co2_kg'  => 80,
                'points_recompense' => 300,
                'actif'             => true,
            ],
            [
                'slug'              => 'economie-energie',
                'titre'             => 'Économie d\'énergie',
                'description'       => 'Réduisez votre consommation d\'électricité de 20%.',
                'difficulte'        => 'Moyen',
                'duree'             => '30 jours',
                'duree_jours'       => 30,
                'reduction_co2'     => '200 kg CO₂',
                'reduction_co2_kg'  => 200,
                'points_recompense' => 200,
                'actif'             => false,
            ],
        ];

        foreach ($challenges as $c) {
            Challenge::create($c);
        }

        // -----------------------------------------------
        // Badges
        // -----------------------------------------------
        $badges = [
            // Badges par challenges complétés
            [
                'nom'             => 'Premier pas',
                'description'     => 'Compléter votre 1er challenge',
                'icone'           => '🌱',
                'couleur'         => '#16a34a',
                'type'            => 'challenges_completes',
                'valeur_requise'  => 1,
                'challenge_slug'  => null,
            ],
            [
                'nom'             => 'Engagé',
                'description'     => 'Compléter 5 challenges',
                'icone'           => '⭐',
                'couleur'         => '#ca8a04',
                'type'            => 'challenges_completes',
                'valeur_requise'  => 5,
                'challenge_slug'  => null,
            ],
            [
                'nom'             => 'Héros vert',
                'description'     => 'Compléter 10 challenges',
                'icone'           => '🏆',
                'couleur'         => '#16a34a',
                'type'            => 'challenges_completes',
                'valeur_requise'  => 10,
                'challenge_slug'  => null,
            ],

            // Badges par points
            [
                'nom'             => 'Éco-curieux',
                'description'     => 'Atteindre 100 points',
                'icone'           => '💡',
                'couleur'         => '#2563eb',
                'type'            => 'points_total',
                'valeur_requise'  => 100,
                'challenge_slug'  => null,
            ],
            [
                'nom'             => 'Éco-warrior',
                'description'     => 'Atteindre 500 points',
                'icone'           => '⚡',
                'couleur'         => '#7c3aed',
                'type'            => 'points_total',
                'valeur_requise'  => 500,
                'challenge_slug'  => null,
            ],

            // Badges par niveau
            [
                'nom'             => 'Niveau 5',
                'description'     => 'Atteindre le niveau 5',
                'icone'           => '🔥',
                'couleur'         => '#dc2626',
                'type'            => 'niveau_atteint',
                'valeur_requise'  => 5,
                'challenge_slug'  => null,
            ],

            // Badges défi spécifique
            [
                'nom'             => 'No Car',
                'description'     => 'Compléter "30 jours sans voiture"',
                'icone'           => '🚲',
                'couleur'         => '#16a34a',
                'type'            => 'defi_specifique',
                'valeur_requise'  => 1,
                'challenge_slug'  => '30-jours-sans-voiture',
            ],
            [
                'nom'             => 'Végé',
                'description'     => 'Compléter "Semaine végétarienne"',
                'icone'           => '🥦',
                'couleur'         => '#16a34a',
                'type'            => 'defi_specifique',
                'valeur_requise'  => 1,
                'challenge_slug'  => 'semaine-vegetarienne',
            ],
        ];

        foreach ($badges as $b) {
            Badge::create($b);
        }
    }
}
