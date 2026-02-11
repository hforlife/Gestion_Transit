<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('expertises', function (Blueprint $table) {
            $table->id();
            $table->string('num_pvc')->nullable();
            $table->string('num_ae')->nullable();
            $table->string('num_cmc')->nullable();

            $table->enum('etat_expertise', ['EN_ATTENTE', 'EFFECTUEE']);
            $table->enum('etat_pvc', ['NON_RECU', 'RECU', 'PAYE']);
            $table->enum('etat_ae', ['NON_VALIDE', 'VALIDE']);
            $table->enum('etat_cmc', ['NON_RECU', 'RECU']);

            $table->enum('status', ['EN_COURS', 'TERMINE']);

            $table->foreignId('expert_id')->constrained('users');
            $table->foreignId('colis_id')->constrained('colis');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expertises');
    }
};
