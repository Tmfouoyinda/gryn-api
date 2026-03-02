<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Challenge;
use App\Services\BadgeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChallengeController extends Controller
{
    public function __construct(private BadgeService $badgeService) {}

    /**
     * GET /api/challenges
     * Liste des challenges actifs
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();

        $challenges = Challenge::where('actif', true)
            ->withCount(['users as participants' => fn($q) => $q->wherePivot('statut', 'en_cours')])
            ->get()
            ->map(function ($challenge) use ($user) {
                // Indique si l'utilisateur connecté participe déjà
                $challenge->statut_user = null;
                if ($user) {
                    $pivot = $user->challenges()->where('challenge_id', $challenge->id)->first()?->pivot;
                    $challenge->statut_user = $pivot?->statut;
                }
                return $challenge;
            });

        return response()->json($challenges);
    }

    /**
     * GET /api/challenges/{id}
     */
    public function show(Challenge $challenge): JsonResponse
    {
        $challenge->loadCount(['users as participants' => fn($q) => $q->wherePivot('statut', 'en_cours')]);
        return response()->json($challenge);
    }

    /**
     * POST /api/challenges/{id}/rejoindre
     */
    public function rejoindre(Challenge $challenge): JsonResponse
    {
        $user = Auth::user();

        $dejaInscrit = $user->challenges()->where('challenge_id', $challenge->id)->exists();

        if ($dejaInscrit) {
            return response()->json(['message' => 'Vous participez déjà à ce challenge.'], 409);
        }

        $user->challenges()->attach($challenge->id, [
            'statut' => 'en_cours',
            'rejoint_le' => now(),
        ]);

        return response()->json([
            'message' => "Vous avez rejoint \"{$challenge->titre}\" !",
        ], 201);
    }

    /**
     * POST /api/challenges/{id}/terminer
     * Marque le challenge terminé, attribue les points et vérifie les badges
     */
    public function terminer(Challenge $challenge): JsonResponse
    {
        $user = Auth::user();

        $participation = $user->challenges()
            ->where('challenge_id', $challenge->id)
            ->wherePivot('statut', 'en_cours')
            ->first();

        if (!$participation) {
            return response()->json(['message' => 'Challenge introuvable ou déjà terminé.'], 404);
        }

        // Marquer comme terminé
        $user->challenges()->updateExistingPivot($challenge->id, [
            'statut' => 'termine',
            'termine_le' => now(),
        ]);

        // Attribuer les points et recalculer le niveau
        $ancienNiveau = $user->niveau;
        $user->ajouterPoints($challenge->points_recompense);
        $niveauUp = $user->niveau > $ancienNiveau;

        // Vérifier si de nouveaux badges sont débloqués
        $user->load('challenges', 'badges'); // refresh relations
        $nouveauxBadges = $this->badgeService->verifierEtAttribuer($user);

        return response()->json([
            'message'           => 'Challenge complété ! Bravo ! 🌱',
            'points_gagnes'     => $challenge->points_recompense,
            'total_points'      => $user->points,
            'niveau'            => $user->niveau,
            'niveau_up'         => $niveauUp,
            'progression'       => $user->progressionNiveau(),
            'prochain_niveau'   => $user->pointsProchainNiveau(),
            'nouveaux_badges'   => $nouveauxBadges,
        ]);
    }

    /**
     * DELETE /api/challenges/{id}/quitter
     */
    public function quitter(Challenge $challenge): JsonResponse
    {
        $user = Auth::user();

        $user->challenges()->updateExistingPivot($challenge->id, [
            'statut' => 'abandonne',
        ]);

        return response()->json(['message' => 'Vous avez quitté le challenge.']);
    }

    /**
     * GET /api/challenges/user/mes-challenges
     * Challenges de l'utilisateur connecté
     */
    public function mesChallenges(): JsonResponse
    {
        $user = Auth::user();

        $enCours = $user->challenges()
            ->wherePivot('statut', 'en_cours')
            ->withPivot(['statut', 'rejoint_le'])
            ->get();

        $termines = $user->challenges()
            ->wherePivot('statut', 'termine')
            ->withPivot(['statut', 'rejoint_le', 'termine_le'])
            ->get();

        return response()->json([
            'en_cours' => $enCours,
            'termines' => $termines,
        ]);
    }

    /**
     * GET /api/challenges/stats
     * Stats globales pour la section "Impact collectif"
     */
    public function stats(): JsonResponse
    {
        $participantsActifs = DB::table('challenge_user')
            ->where('statut', 'en_cours')
            ->distinct('user_id')
            ->count('user_id');

        $co2Economise = DB::table('challenge_user')
            ->join('challenges', 'challenge_user.challenge_id', '=', 'challenges.id')
            ->where('challenge_user.statut', 'termine')
            ->sum('challenges.reduction_co2_kg');

        $challengesCompletes = DB::table('challenge_user')
            ->where('statut', 'termine')
            ->count();

        return response()->json([
            'participants_actifs'   => $participantsActifs,
            'co2_economise_kg'      => $co2Economise,
            'challenges_completes'  => $challengesCompletes,
        ]);
    }
}
