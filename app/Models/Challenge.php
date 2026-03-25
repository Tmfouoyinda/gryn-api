<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Challenge extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'duration_days', 'difficulty',
        'co2_reduction_kg', 'points_reward', 'badge_id', 'is_active',
    ];

    protected $casts = [
        'co2_reduction_kg' => 'decimal:2',
        'is_active'        => 'boolean',
    ];

    public function badge(): BelongsTo
    {
        return $this->belongsTo(Badge::class);
    }

   
    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'challenge_user')
            ->withPivot(['status', 'joined_at', 'completed_at', 'progress'])
            ->withTimestamps();
    }

    /**
     * Scope : retourne uniquement les challenges actifs.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
