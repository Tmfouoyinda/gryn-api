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
        'transport_voiture',
        'transport_train',
        'transport_bus',
        'emissions_transport',
        'alimentation_regime',
        'alimentation_kg_viande',
        'alimentation_kg_poisson',
        'emissions_alimentation',
        'energie_electricite',
        'energie_gaz',
        'energie_renouvelable',
        'emissions_energie',
        'equipements_nombre',
        'equipements_montant',
        'emissions_consommation',
        'total_emissions',
    ];

    protected $casts = [
        'energie_renouvelable' => 'boolean',
        'transport_voiture' => 'decimal:2',
        'transport_train' => 'decimal:2',
        'transport_bus' => 'decimal:2',
        'emissions_transport' => 'decimal:2',
        'alimentation_kg_viande' => 'decimal:2',
        'alimentation_kg_poisson' => 'decimal:2',
        'energie_electricite' => 'decimal:2',
        'energie_gaz' => 'decimal:2',
        'emissions_alimentation' => 'decimal:2',
        'emissions_energie' => 'decimal:2',
        'emissions_consommation' => 'decimal:2',
        'total_emissions' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}