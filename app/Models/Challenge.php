<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Challenge extends Model
{
    protected $fillable = [
        'slug',
        'titre',
        'description',
        'difficulte',
        'duree',
        'duree_jours',
        'reduction_co2',
        'reduction_co2_kg',
        'points_recompense',
        'actif',
    ];

    protected $casts = [
        'actif' => 'boolean',
        'reduction_co2_kg' => 'float',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'challenge_user')
            ->withPivot(['statut', 'rejoint_le', 'termine_le'])
            ->withTimestamps();
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'challenge_user')
            ->wherePivot('statut', 'en_cours');
    }
}
