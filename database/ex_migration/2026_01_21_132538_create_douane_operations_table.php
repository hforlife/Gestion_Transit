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
        Schema::create('douane_operations', function (Blueprint $table) {
            $table->id();
            $table->string('num_t1')->nullable();
            $table->enum('etat_t1', ['FOURNI', 'PAYE'])->nullable();
            $table->string('declaration_reference')->nullable();

            $table->date('date_entree_douane')->nullable();
            $table->date('date_sortie_douane')->nullable();
            $table->enum('status_colis', ['EN_ATTENTE', 'ENTRE', 'SORTI']);

            $table->foreignId('colis_id')->constrained('colis');
            $table->foreignId('agent_id')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('douane_operations');
    }
};
