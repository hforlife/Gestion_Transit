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
        Schema::create('port_operations', function (Blueprint $table) {
            $table->id();
            $table->date('date_entree_port')->nullable();
            $table->date('date_sortie_port')->nullable();
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
        Schema::dropIfExists('port_operations');
    }
};
