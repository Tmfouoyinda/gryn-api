<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'password',
        'points',
        'niveau',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // -------------------------
    // Relations
    // -------------------------

    public function challenges(): BelongsToMany
    {
        return $this->belongsToMany(Challenge::class, 'challenge_user')
            ->withPivot(['statut', 'rejoint_le', 'termine_le'])
            ->withTimestamps();
    }

    public function badges(): BelongsToMany
    {
        return $this->belongsToMany(Badge::class, 'badge_user')
            ->withPivot('obtenu_le')
            ->withTimestamps();
    }

    // -------------------------
    // Logique points & niveaux
    // -------------------------

    // Seuils de points pour chaque niveau
    public static function seuilsNiveaux(): array
    {
        return [
            1 => 0,
            2 => 100,
            3 => 300,
            4 => 600,
            5 => 1000,
            6 => 1500,
            7 => 2200,
            8 => 3000,
            9 => 4000,
            10 => 5000,
        ];
    }

    // Calcule le niveau selon les points
    public static function calculerNiveau(int $points): int
    {
        $niveau = 1;
        foreach (self::seuilsNiveaux() as $lvl => $seuil) {
            if ($points >= $seuil) {
                $niveau = $lvl;
            }
        }
        return $niveau;
    }

    // Ajoute des points et met à jour le niveau
    public function ajouterPoints(int $points): void
    {
        $this->points += $points;
        $this->niveau = self::calculerNiveau($this->points);
        $this->save();
    }

    // Points nécessaires pour le prochain niveau
    public function pointsProchainNiveau(): ?int
    {
        $seuils = self::seuilsNiveaux();
        $prochainNiveau = $this->niveau + 1;
        return $seuils[$prochainNiveau] ?? null; // null = niveau max atteint
    }

    // Progression en % vers le prochain niveau
    public function progressionNiveau(): int
    {
        $seuils = self::seuilsNiveaux();
        $seuilActuel = $seuils[$this->niveau] ?? 0;
        $seuilSuivant = $seuils[$this->niveau + 1] ?? null;

        if (!$seuilSuivant) return 100; // niveau max

        $progression = ($this->points - $seuilActuel) / ($seuilSuivant - $seuilActuel) * 100;
        return (int) min(100, max(0, $progression));
    }
}
