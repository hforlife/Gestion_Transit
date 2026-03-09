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

            // BL
            $table->string('numero_bl')->unique();
            $table->string('description')->nullable();

            $table->foreignId('id_type_colis')->constrained('type_colis');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('id_port')->constrained('ports');

            $table->enum('etat_colis', [
                'BL_ENREGISTRE',
                'AU_PORT',
                'EN_COURS',
                'TERMINE'
            ])->default('BL_ENREGISTRE');

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
