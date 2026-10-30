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
        Schema::create('rappels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rendez_vous_id')->constrained('rendez_vous')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamp('date_prevue');
            $table->enum('statut', ['en_attente', 'envoye', 'echec', 'annule'])->default('en_attente');
            $table->timestamp('date_envoi')->nullable();
            $table->timestamps();

            $table->index('rendez_vous_id');
            $table->index('user_id');
            $table->index(['statut', 'date_prevue']);
            $table->index('date_prevue');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rappels');
    }
};
