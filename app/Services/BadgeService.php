<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\User;

class BadgeService
{
    /**
     * Vérifie et attribue tous les badges mérités à un utilisateur.
     * À appeler après chaque action (complétion de challenge, gain de points, etc.)
     */
    public function verifierEtAttribuer(User $user): array
    {
        $nouveauxBadges = [];
        $badgesDejaObtenu = $user->badges->pluck('id')->toArray();
        $allBadges = Badge::all();

        foreach ($allBadges as $badge) {
            // Skip si déjà obtenu
            if (in_array($badge->id, $badgesDejaObtenu)) {
                continue;
            }

            if ($this->conditionRemplie($user, $badge)) {
                $user->badges()->attach($badge->id, ['obtenu_le' => now()]);
                $nouveauxBadges[] = $badge;
            }
        }

        return $nouveauxBadges;
    }

    private function conditionRemplie(User $user, Badge $badge): bool
    {
        return match ($badge->type) {
            'challenges_completes' => $this->challengesCompletes($user) >= $badge->valeur_requise,
            'points_total'         => $user->points >= $badge->valeur_requise,
            'niveau_atteint'       => $user->niveau >= $badge->valeur_requise,
            'defi_specifique'      => $this->aCompleteDefi($user, $badge->challenge_slug),
            default                => false,
        };
    }

    private function challengesCompletes(User $user): int
    {
        return $user->challenges()->wherePivot('statut', 'termine')->count();
    }

    private function aCompleteDefi(User $user, ?string $slug): bool
    {
        if (!$slug) return false;

        return $user->challenges()
            ->where('slug', $slug)
            ->wherePivot('statut', 'termine')
            ->exists();
    }
}
