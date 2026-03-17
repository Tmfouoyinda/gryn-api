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
            $table->string('title');
            $table->text('description');
            $table->unsignedInteger('duration_days');
            $table->enum('difficulty', ['Facile', 'Moyen', 'Difficile']);
            $table->decimal('co2_reduction_kg', 8, 2);
            $table->unsignedInteger('points_reward')->default(100);
            // Badge décerné à la complétion (optionnel)
            $table->foreignId('badge_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('challenges');
    }
};
