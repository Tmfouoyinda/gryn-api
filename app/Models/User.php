<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'name', 'first_name', 'last_name',
        'email', 'password',
        'points', 'level',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'points'            => 'integer',
            'level'             => 'integer',
        ];
    }

    // ─── Relations ─────────────────────────────────────────────────────────

    public function calculations(): HasMany
    {
        return $this->hasMany(Calculation::class);
    }

    public function challenges(): BelongsToMany
    {
        return $this->belongsToMany(Challenge::class, 'challenge_user')
            ->withPivot(['status', 'joined_at', 'completed_at', 'progress'])
            ->withTimestamps();
    }

    public function badges(): BelongsToMany
    {
        return $this->belongsToMany(Badge::class, 'badge_user')
            ->withPivot('earned_at')
            ->withTimestamps();
    }

    // ─── Méthodes métier ───────────────────────────────────────────────────

    /**
     * Ajoute des points et recalcule le niveau.
     * Règle : level = floor(points / 500) + 1, plafonné à 10.
     */
    public function addPoints(int $points): void
    {
        $this->points += $points;
        $this->level   = min(10, (int) floor($this->points / 500) + 1);
        $this->save();
    }

    /**
     * Attribue un badge si l'utilisateur ne le possède pas déjà.
     * Retourne true si le badge a été accordé pour la première fois.
     */
    public function awardBadge(Badge $badge): bool
    {
        if ($this->badges()->where('badge_id', $badge->id)->exists()) {
            return false;
        }

        $this->badges()->attach($badge->id, ['earned_at' => now()]);
        $this->addPoints($badge->points_reward);

        return true;
    }

    /**
     * Envoie le lien de réinitialisation du mot de passe vers le frontend.
     */
    public function sendPasswordResetNotification($token): void
    {
        $url = config('app.frontend_url')
            . '/reset-password?token=' . $token
            . '&email=' . urlencode($this->email);

        $this->notify(new ResetPasswordNotification($url));
    }
}
