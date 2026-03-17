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
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Transport — km/mois
            $table->decimal('transport_voiture', 10, 2)->default(0);
            $table->decimal('transport_train',   10, 2)->default(0);
            $table->decimal('transport_bus',     10, 2)->default(0);
            $table->decimal('transport_avion',   10, 2)->default(0);
            $table->decimal('transport_velo',    10, 2)->default(0);
            $table->decimal('transport_moto',    10, 2)->default(0);

            // Alimentation
            $table->string('alimentation_regime');
            $table->decimal('alimentation_kg_viande',  8, 2)->default(0);
            $table->decimal('alimentation_kg_poulet',  8, 2)->default(0);
            $table->decimal('alimentation_kg_poisson', 8, 2)->default(0);

            // Énergie — kWh/mois
            $table->decimal('energie_electricite', 10, 2)->default(0);
            $table->decimal('energie_gaz',         10, 2)->default(0);
            $table->boolean('energie_renouvelable')->default(false);

            // Consommation
            $table->string('consommation_niveau');

            // Émissions calculées — kg CO₂/mois
            $table->decimal('emissions_transport',    10, 2)->default(0);
            $table->decimal('emissions_alimentation', 10, 2)->default(0);
            $table->decimal('emissions_energie',      10, 2)->default(0);
            $table->decimal('emissions_consommation', 10, 2)->default(0);
            $table->decimal('total_emissions',        10, 2)->default(0);

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
