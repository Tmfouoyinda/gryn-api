<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('challenges', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique(); // ex: "30-jours-sans-voiture"
            $table->string('titre');
            $table->text('description');
            $table->enum('difficulte', ['Facile', 'Moyen', 'Difficile']);
            $table->string('duree'); // ex: "30 jours"
            $table->integer('duree_jours');
            $table->string('reduction_co2'); // ex: "150 kg CO₂"
            $table->float('reduction_co2_kg');
            $table->integer('points_recompense'); // points gagnés en complétant
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });

        Schema::create('challenge_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('challenge_id')->constrained()->onDelete('cascade');
            $table->enum('statut', ['en_cours', 'termine', 'abandonne'])->default('en_cours');
            $table->timestamp('rejoint_le')->useCurrent();
            $table->timestamp('termine_le')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'challenge_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('challenge_user');
        Schema::dropIfExists('challenges');
    }
};
