<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('challenge_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('challenge_id')->constrained()->cascadeOnDelete();
            // joined | completed | abandoned
            $table->enum('status', ['joined', 'completed', 'abandoned'])->default('joined');
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            // Progression en % (0-100), mise à jour manuellement par l'utilisateur
            $table->unsignedTinyInteger('progress')->default(0);
            $table->timestamps();

            // Un utilisateur ne peut rejoindre un challenge qu'une seule fois
            $table->unique(['user_id', 'challenge_id']);
            $table->index('user_id');
            $table->index('challenge_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('challenge_user');
    }
};
