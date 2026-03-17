<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Models\Challenge;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class StatsController extends Controller
{
    /**
     * Statistiques publiques affichées sur la page d'accueil et la page À propos.
     * Mise en cache 1 heure pour éviter des requêtes répétées.
     */
    public function index(): JsonResponse
    {
        $stats = Cache::remember('public_stats', 3600, function () {
            return [
                ['numbers' => User::count() . '+',      'description' => 'Utilisateurs actifs'],
                ['numbers' => '2M+',                    'description' => 'Tonnes de CO₂ réduites'],
                ['numbers' => Challenge::count() . '+', 'description' => 'Challenges relevés'],
                ['numbers' => Badge::count() . '+',     'description' => 'Badges obtenus'],
            ];
        });

        return response()->json($stats);
    }
}
