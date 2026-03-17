<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Calculation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        // Transport (km/mois)
        'transport_voiture', 'transport_train', 'transport_bus',
        'transport_avion', 'transport_velo', 'transport_moto',
        // Alimentation
        'alimentation_regime', 'alimentation_kg_viande',
        'alimentation_kg_poulet', 'alimentation_kg_poisson',
        // Énergie (kWh/mois)
        'energie_electricite', 'energie_gaz', 'energie_renouvelable',
        // Consommation
        'consommation_niveau',
        // Émissions calculées (kg CO₂/mois)
        'emissions_transport', 'emissions_alimentation',
        'emissions_energie', 'emissions_consommation', 'total_emissions',
    ];

    protected $casts = [
        'energie_renouvelable'    => 'boolean',
        'transport_voiture'       => 'decimal:2',
        'transport_train'         => 'decimal:2',
        'transport_bus'           => 'decimal:2',
        'transport_avion'         => 'decimal:2',
        'transport_velo'          => 'decimal:2',
        'transport_moto'          => 'decimal:2',
        'alimentation_kg_viande'  => 'decimal:2',
        'alimentation_kg_poulet'  => 'decimal:2',
        'alimentation_kg_poisson' => 'decimal:2',
        'energie_electricite'     => 'decimal:2',
        'energie_gaz'             => 'decimal:2',
        'emissions_transport'     => 'decimal:2',
        'emissions_alimentation'  => 'decimal:2',
        'emissions_energie'       => 'decimal:2',
        'emissions_consommation'  => 'decimal:2',
        'total_emissions'         => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
