<?php

namespace App\Http\Controllers;

use App\Http\Resources\BadgeResource;
use App\Models\Badge;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class BadgeController extends Controller
{
    /**
     * Tous les badges avec le statut earned de l'utilisateur connecté.
     */
    public function index(): JsonResponse
    {
        $earnedBadges = Auth::user()->badges()->get()->keyBy('id');
        $earnedIds    = $earnedBadges->keys()->toArray();

        $badges = Badge::all()->map(function (Badge $badge) use ($earnedIds, $earnedBadges) {
            $earned = in_array($badge->id, $earnedIds);

            return [
                'id'            => $badge->id,
                'name'          => $badge->name,
                'icon'          => $badge->icon,
                'description'   => $badge->description,
                'points_reward' => $badge->points_reward,
                'earned'        => $earned,
                'earned_at'     => $earned ? $earnedBadges[$badge->id]->pivot->earned_at : null,
            ];
        });

        return response()->json(['success' => true, 'data' => $badges->values()]);
    }
}
