<?php

namespace App\Http\Controllers;

use App\Http\Resources\ChallengeResource;
use App\Models\Challenge;
use App\Services\BadgeAwarderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class ChallengeController extends Controller
{
    public function __construct(private readonly BadgeAwarderService $badgeAwarder) {}

    /**
     * Tous les challenges actifs avec le statut de participation de l'utilisateur.
     */
    public function index(): AnonymousResourceCollection
    {
        $challenges = Challenge::active()
            ->with('badge:id,name,icon')
            ->withCount('participants')
            ->get();

        // Charger la participation de l'utilisateur connecté si authentifié
        if ($userId = Auth::id()) {
            $challenges->each(function (Challenge $challenge) use ($userId) {
                $challenge->setRelation(
                    'userParticipation',
                    $challenge->participants()->where('user_id', $userId)->first()
                );
            });
        }

        return ChallengeResource::collection($challenges);
    }

    /**
     * Challenges auxquels l'utilisateur connecté participe.
     */
    public function myChallenges(): AnonymousResourceCollection
    {
        $challenges = Auth::user()
            ->challenges()
            ->with('badge:id,name,icon')
            ->withCount('participants')
            ->get();

        return ChallengeResource::collection($challenges);
    }

    /**
     * Rejoindre un challenge actif.
     */
    public function join(int $id): JsonResponse
    {
        $challenge = Challenge::active()->findOrFail($id);
        $user      = Auth::user();

        if ($user->challenges()->where('challenge_id', $id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous participez déjà à ce challenge.',
            ], 409);
        }

        $user->challenges()->attach($id, [
            'status'    => 'joined',
            'joined_at' => now(),
            'progress'  => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Vous avez rejoint le challenge avec succès.',
        ], 201);
    }

    /**
     * Mettre à jour la progression d'un challenge (0-100).
     */
    public function updateProgress(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'progress' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        $user = Auth::user();
        $participation = $user->challenges()
            ->where('challenge_id', $id)
            ->where('challenge_user.status', 'joined')
            ->first();

        if (! $participation) {
            return response()->json([
                'success' => false,
                'message' => 'Vous ne participez pas à ce challenge ou il est déjà terminé.',
            ], 404);
        }

        $user->challenges()->updateExistingPivot($id, [
            'progress' => $request->progress,
        ]);

        return response()->json([
            'success'  => true,
            'message'  => 'Progression mise à jour.',
            'progress' => $request->progress,
        ]);
    }

    /**
     * Terminer un challenge : attribue les points et le badge associé.
     */
    public function complete(int $id): JsonResponse
    {
        $challenge = Challenge::with('badge')->findOrFail($id);
        $user      = Auth::user();

        $participation = $user->challenges()
            ->where('challenge_id', $id)
            ->where('challenge_user.status', 'joined')
            ->first();

        if (! $participation) {
            return response()->json([
                'success' => false,
                'message' => 'Vous ne participez pas à ce challenge ou il est déjà terminé.',
            ], 404);
        }

        $user->challenges()->updateExistingPivot($id, [
            'status'       => 'completed',
            'progress'     => 100,
            'completed_at' => now(),
        ]);

        $user->addPoints($challenge->points_reward);

        // Badge lié directement au challenge
        $challengeBadge = null;
        if ($challenge->badge && $user->awardBadge($challenge->badge)) {
            $challengeBadge = $challenge->badge;
        }

        // Badges débloqués par conditions automatiques
        $newBadges = collect($this->badgeAwarder->checkAndAward($user->fresh()));

        if ($challengeBadge && ! $newBadges->contains('id', $challengeBadge->id)) {
            $newBadges->prepend($challengeBadge);
        }

        $freshUser = $user->fresh();

        return response()->json([
            'success'    => true,
            'message'    => 'Challenge terminé ! Félicitations.',
            'points_won' => $challenge->points_reward,
            'new_badges' => $newBadges->values(),
            'user'       => [
                'points' => $freshUser->points,
                'level'  => $freshUser->level,
            ],
        ]);
    }
}
