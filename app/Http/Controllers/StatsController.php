<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Models\Challenge;
use App\Models\Badge;

class StatsController extends Controller
{
    public function getStats(Request $request): JsonResponse
    {
        $stats = Cache::remember('public_stats', 3600, function () {
            return [
                ['numbers' => User::count() . '+',     'description' => 'Utilisateurs actifs'],
                ['numbers' => '2M+',                   'description' => 'Tonnes de CO² réduites'],
                ['numbers' => Challenge::count() . '+', 'description' => 'Challenges relevés'],
                ['numbers' => Badge::count() . '+',     'description' => 'Badges obtenus'],
            ];
        });

        return response()->json($stats);
    }
}
