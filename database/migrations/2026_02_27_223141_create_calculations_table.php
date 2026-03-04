<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calculations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Transport
            $table->decimal('transport_voiture', 10, 2)->default(0);
            $table->decimal('transport_train', 10, 2)->default(0);
            $table->decimal('transport_bus', 10, 2)->default(0);
            $table->decimal('emissions_transport', 10, 2)->default(0);
            
            // Alimentation (AVEC les kg viande/poisson directement)
            $table->string('alimentation_regime');
            $table->decimal('alimentation_kg_viande', 8, 2)->default(0);
            $table->decimal('alimentation_kg_poisson', 8, 2)->default(0);
            $table->decimal('emissions_alimentation', 10, 2)->default(0);
            
            // Énergie
            $table->decimal('energie_electricite', 10, 2)->default(0);
            $table->decimal('energie_gaz', 10, 2)->default(0);
            $table->boolean('energie_renouvelable')->default(false);
            $table->decimal('emissions_energie', 10, 2)->default(0);
            
            // Équipements
            $table->string('equipements_nombre');
            $table->integer('equipements_montant');
            $table->decimal('emissions_consommation', 10, 2)->default(0);
            
            // Total
            $table->decimal('total_emissions', 10, 2)->default(0);
            
            $table->timestamps();
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calculations');
    }
};