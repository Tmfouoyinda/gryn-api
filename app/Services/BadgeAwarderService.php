<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\User;

/**
 * Centralise la logique d'attribution automatique des badges.
 *
 * Appelé après chaque action susceptible de débloquer un badge :
 * complétion d'un challenge, nouveau calcul d'empreinte, etc.
 */
class BadgeAwarderService
{
    /**
     * Vérifie et attribue tous les badges éligibles pour un utilisateur.
     *
     * @return Badge[]
     */
    public function checkAndAward(User $user): array
    {
        $user->loadCount([
            'calculations',
            'challenges as completed_challenges_count' => fn ($q) => $q->wherePivot('status', 'completed'),
        ]);

        $newBadges = [];

        foreach (Badge::all() as $badge) {
            if ($this->isEligible($user, $badge) && $user->awardBadge($badge)) {
                $newBadges[] = $badge;
            }
        }

        return $newBadges;
    }

    /**
     * Vérifie si l'utilisateur remplit la condition d'un badge donné.
     */
    private function isEligible(User $user, Badge $badge): bool
    {
        return match ($badge->condition_type) {
            'calculations_count'  => $user->calculations_count          >= $badge->condition_value,
            'challenge_completed' => $user->completed_challenges_count  >= $badge->condition_value,
            default               => false,
        };
    }
}
