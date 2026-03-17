<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CalculationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                      => $this->id,
            'total_emissions'         => (float) $this->total_emissions,
            'emissions_transport'     => (float) $this->emissions_transport,
            'emissions_alimentation'  => (float) $this->emissions_alimentation,
            'emissions_energie'       => (float) $this->emissions_energie,
            'emissions_consommation'  => (float) $this->emissions_consommation,
            'alimentation_regime'     => $this->alimentation_regime,
            'consommation_niveau'     => $this->consommation_niveau,
            'energie_renouvelable'    => $this->energie_renouvelable,
            'created_at'              => $this->created_at->toIso8601String(),
        ];
    }
}
