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
        Schema::create('user_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->text('fcm_token');
            $table->enum('plateforme', ['android', 'ios']);
            $table->boolean('actif')->default(true);
            $table->timestamp('derniere_connexion')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('actif');
            $table->index(['user_id', 'actif']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_devices');
    }
};
