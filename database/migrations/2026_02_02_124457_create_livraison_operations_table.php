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
        Schema::create('livraison_operations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colis_id')->constrained();
            $table->foreignId('agent_id')->constrained('users');
            $table->date('date_livraison')->nullable();
            $table->enum('statut', ['EN_ROUTE','LIVREE'])->default('EN_ROUTE');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('livraison_operations');
    }
};
