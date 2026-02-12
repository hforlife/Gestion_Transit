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
        Schema::create('colis', function (Blueprint $table) {
            $table->id();

            // Enregistrement du colis
            $table->string('numero_bl')->unique();
            $table->string('description')->nullable();
            $table->foreignId('id_type_colis')->constrained('type_colis');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('id_port')->constrained('ports');
            $table->foreignId('client_id')->constrained('clients');
            $table->enum('etat_colis', [
                'BL_ENREGISTRE',
                'AU_PORT',
                'A_LA_DOUANE',
                'EXPERTISE',
                'EN_ROUTE',
                'LIVRE',
                'CLOTURE'
            ])->default('BL_ENREGISTRE');

            // Port
            $table->date('date_entree_port')->nullable();
            $table->date('date_sortie_port')->nullable();
            $table->enum('status_colis_port', ['EN_ATTENTE', 'ENTRE', 'SORTI']);

            // Douane
            $table->string('num_t1')->nullable();
            $table->enum('etat_t1', ['FOURNI', 'PAYE'])->nullable();
            $table->string('declaration_reference')->nullable();
            $table->date('date_entree_douane')->nullable();
            $table->date('date_sortie_douane')->nullable();
             $table->enum('status_colis_douane', ['EN_ATTENTE', 'ENTRE', 'SORTI']);

            // Expertise
            $table->string('num_pvc')->nullable();
            $table->string('num_ae')->nullable();
            $table->string('num_cmc')->nullable();
            $table->enum('etat_expertise', ['EN_ATTENTE', 'EFFECTUEE']);
            $table->enum('etat_pvc', ['NON_RECU', 'RECU', 'PAYE']);
            $table->enum('etat_ae', ['NON_VALIDE', 'VALIDE']);
            $table->enum('etat_cmc', ['NON_RECU', 'RECU']);
            $table->enum('status', ['EN_COURS', 'TERMINE']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('colis');
    }
};
