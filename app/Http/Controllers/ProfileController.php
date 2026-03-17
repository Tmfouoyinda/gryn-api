<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Profil complet : infos, badges obtenus, dernière émission, statistiques.
     */
    public function show(): JsonResponse
    {
        $user = Auth::user()->load([
            'badges',
            'calculations' => fn ($q) => $q->latest()->limit(6),
        ]);

        $completedChallengesCount = $user->challenges()
            ->wherePivot('status', 'completed')
            ->count();

        return response()->json([
            'success' => true,
            'data'    => [
                'id'                         => $user->id,
                'name'                       => $user->name,
                'first_name'                 => $user->first_name,
                'last_name'                  => $user->last_name,
                'email'                      => $user->email,
                'points'                     => $user->points,
                'level'                      => $user->level,
                'completed_challenges_count' => $completedChallengesCount,
                'latest_emission'            => $user->calculations->first()?->total_emissions,
                'badges'                     => $user->badges->map(fn ($b) => [
                    'id'        => $b->id,
                    'name'      => $b->name,
                    'icon'      => $b->icon,
                    'earned_at' => $b->pivot->earned_at,
                    'earned'    => true,
                ]),
                'recent_calculations'        => $user->calculations->map(fn ($c) => [
                    'id'              => $c->id,
                    'total_emissions' => (float) $c->total_emissions,
                    'created_at'      => $c->created_at->toIso8601String(),
                ]),
            ],
        ]);
    }

    /**
     * Met à jour les informations du profil de l'utilisateur connecté.
     */
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = Auth::user();
        $user->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Profil mis à jour avec succès.',
            'data'    => new UserResource($user->fresh()),
        ]);
    }
}
