<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('description');
            $table->string('icone'); // ex: "🌱", "🚗", "♻️" ou un nom d'icône
            $table->string('couleur')->default('#16a34a'); // couleur hex
            // Condition pour obtenir le badge
            $table->enum('type', ['challenges_completes', 'points_total', 'niveau_atteint', 'defi_specifique']);
            $table->integer('valeur_requise'); // ex: 5 challenges, 500 points, niveau 3
            $table->string('challenge_slug')->nullable(); // pour type = defi_specifique
            $table->timestamps();
        });

        Schema::create('badge_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('badge_id')->constrained()->onDelete('cascade');
            $table->timestamp('obtenu_le')->useCurrent();
            $table->timestamps();

            $table->unique(['user_id', 'badge_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('badge_user');
        Schema::dropIfExists('badges');
    }
};
