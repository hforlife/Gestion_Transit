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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            // $table->foreignId('colis_id')->constrained();
            $table->foreignId('id_dossier_transit')->constrained('dossier_transits');
            $table->enum('type_document',['BL', 'T1', 'Déclaration','PVC', 'AE', 'CMC', 'Carte grise', 'Plaque']); // PVC, CMC, Carte grise...
            $table->string('fichier');
            $table->boolean('valide')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
