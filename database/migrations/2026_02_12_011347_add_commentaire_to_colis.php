<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('colis', function (Blueprint $table) {
            $table->date('date_livraison')->nullable()->after('date_sortie_douane');
            $table->enum('status_colis_livraison', ['EN_ATTENTE', 'LIVRE'])->default('EN_ATTENTE')->after('date_livraison');
            $table->string('commentaires_cloture')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('colis', function (Blueprint $table) {
            //
        });
    }
};
