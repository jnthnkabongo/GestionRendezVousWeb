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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('rendez_vous_id')->nullable()->constrained('rendez_vous')->onUpdate('cascade')->onDelete('set null');
            $table->string('titre');
            $table->text('message');
            $table->enum('type', ['rappel_rendez_vous', 'nouveau_rendez_vous', 'modification_rendez_vous', 'annulation_rendez_vous']);
            $table->boolean('lue')->default(false);
            $table->timestamp('date_envoi')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('rendez_vous_id');
            $table->index(['user_id', 'lue']);
            $table->index('date_envoi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
