<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Badge extends Model
{
    protected $fillable = [
        'nom',
        'description',
        'icone',
        'couleur',
        'type',
        'valeur_requise',
        'challenge_slug',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'badge_user')
            ->withPivot('obtenu_le')
            ->withTimestamps();
    }
}
