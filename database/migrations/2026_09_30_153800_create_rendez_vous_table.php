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
        Schema::create('rendez_vous', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignId('cree_par')->constrained('users')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignId('chef_id')->constrained('users')->onUpdate('cascade')->onDelete('restrict');
            $table->date('date_rendez_vous');
            $table->time('heure_debut');
            $table->time('heure_fin');
            $table->string('objet');
            $table->text('description')->nullable();
            $table->enum('statut', ['planifie', 'confirme', 'termine', 'annule'])->default('planifie');
            $table->boolean('rappel_active')->default(true);
            $table->unsignedInteger('rappel_minutes')->default(30);
            $table->timestamps();

            $table->index('client_id');
            $table->index('cree_par');
            $table->index(['chef_id', 'date_rendez_vous']);
            $table->index('date_rendez_vous');
            $table->index('statut');
            $table->index(['date_rendez_vous', 'heure_debut']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rendez_vous');
    }
};
