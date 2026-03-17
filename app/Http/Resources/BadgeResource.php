<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BadgeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'icon'          => $this->icon,
            'description'   => $this->description,
            'points_reward' => $this->points_reward,
            // Présents uniquement quand chargés via la relation badge_user
            'earned'        => $this->whenPivotLoaded('badge_user', fn () => true, false),
            'earned_at'     => $this->whenPivotLoaded('badge_user', fn () => $this->pivot->earned_at),
        ];
    }
}
