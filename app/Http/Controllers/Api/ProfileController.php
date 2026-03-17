<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * GET /api/profil
     * Retourne les infos complètes du profil (points, niveau, badges, progression)
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();
        $user->load('badges', 'challenges');

        return response()->json([
            'id'                => $user->id,
            'name'              => $user->name,
            'email'             => $user->email,
            'points'            => $user->points,
            'niveau'            => $user->niveau,
            'progression'       => $user->progressionNiveau(),
            'prochain_niveau'   => $user->pointsProchainNiveau(),
            'badges'            => $user->badges,
            'challenges_termines' => $user->challenges()
                ->wherePivot('statut', 'termine')
                ->count(),
        ]);
    }
}
