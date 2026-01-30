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
        Schema::create('dossier_transits', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->string('nom');
            $table->string('repertoire')->nullable();
            $table->date('date_depot')->nullable();
            $table->enum('status', ['OUVERT', 'EN_COURS', 'CLOTURE']);

            $table->foreignId('colis_id')->constrained('colis');
            $table->foreignId('id_type_dossier')->constrained('type_dossiers');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dossier_transits');
    }
};
