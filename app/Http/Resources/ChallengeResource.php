<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChallengeResource extends JsonResource
{
   public function toArray(Request $request): array
{
    $pivot = null;

    if ($this->pivot && $this->pivot->status) {
        $pivot = $this->pivot;
    } elseif ($this->relationLoaded('userParticipation') && $this->userParticipation) {
        $pivot = $this->userParticipation->pivot;
    }

    return [
        'id'                 => $this->id,
        'title'              => $this->title,
        'description'        => $this->description,
        'duration_days'      => $this->duration_days,
        'difficulty'         => $this->difficulty,
        'co2_reduction_kg'   => (float) $this->co2_reduction_kg,
        'points_reward'      => $this->points_reward,
        'participants_count' => $this->participants_count ?? 0,

        'badge' => $this->whenLoaded('badge', fn () => $this->badge ? [
            'id'   => $this->badge->id,
            'name' => $this->badge->name,
            'icon' => $this->badge->icon,
        ] : null),

        'my_participation' => $pivot ? [
            'status'       => $pivot->status,
            'progress'     => $pivot->progress,
            'joined_at'    => $pivot->joined_at,
            'completed_at' => $pivot->completed_at,
        ] : null,
    ];
}
    
}
