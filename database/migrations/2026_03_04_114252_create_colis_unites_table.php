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
        Schema::create('colis_unites', function (Blueprint $table) {

            $table->id();

            $table->foreignId('colis_id')->constrained('colis')->cascadeOnDelete();

            // type d'unité
            $table->enum('type', [
                'CONTENEUR',
                'CHASSIS',
                'CHASSIS_VOITURE',
                'CHASSIS_MACHINE'
            ]);

            // identifiants
            $table->string('numero_conteneur')->nullable();
            $table->string('numero_chassis')->nullable();
            $table->string('vin')->nullable();

            /*
            =================
            PORT
            =================
            */

            $table->date('date_arrivee_port')->nullable();
            $table->date('date_sortie_port')->nullable();

            $table->enum('status_port', [
                'EN_ATTENTE',
                'AU_PORT',
                'SORTI'
            ])->default('EN_ATTENTE');

            /*
            =================
            DOUANE
            =================
            */

            $table->string('num_t1')->nullable();

            $table->enum('etat_t1', [
                'NON_FOURNI',
                'FOURNI',
                'PAYE'
            ])->default('NON_FOURNI');

            $table->string('declaration_reference')->nullable();

            $table->date('date_entree_douane')->nullable();
            $table->date('date_sortie_douane')->nullable();

            $table->enum('status_douane', [
                'EN_ATTENTE',
                'ENTRE',
                'SORTI'
            ])->default('EN_ATTENTE');

            /*
            =================
            EXPERTISE
            =================
            */

            $table->enum('etat_expertise', [
                'EN_ATTENTE',
                'EFFECTUEE'
            ])->default('EN_ATTENTE');

            $table->string('num_pvc')->nullable();

            $table->enum('etat_pvc', [
                'NON_RECU',
                'RECU',
                'PAYE'
            ])->default('NON_RECU');

            $table->string('num_ae')->nullable();

            $table->enum('etat_ae', [
                'NON_VALIDE',
                'VALIDE'
            ])->default('NON_VALIDE');

            $table->string('num_cmc')->nullable();

            $table->enum('etat_cmc', [
                'NON_RECU',
                'RECU'
            ])->default('NON_RECU');

            /*
            =================
            LIVRAISON
            =================
            */

            $table->date('date_livraison')->nullable();

            $table->enum('etat', [
                'AU_PORT',
                'A_LA_DOUANE',
                'EXPERTISE',
                'EN_ROUTE',
                'LIVRE'
            ])->default('AU_PORT');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('colis_unites');
    }
};
