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
        Schema::create('tracking_events', function (Blueprint $table) {
            $table->id();

            $table->morphs('trackable');
            // colis ou dossier_transit

            $table->string('step');
            // ex: BL_ENREGISTRE, AU_PORT, PVC_RECU, LIVRE...

            $table->string('label');
            // ex: "BL enregistré", "PVC reçu"

            $table->enum('status', ['OK', 'BLOQUE', 'EN_ATTENTE'])->default('OK');

            $table->text('commentaire')->nullable();

            $table->foreignId('user_id')->nullable()->constrained();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracking_events');
    }
};
