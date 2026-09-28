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
        Schema::create('chat_conversations', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->unique(); // Identifiant unique de session
            $table->unsignedBigInteger('user_id')->nullable(); // ID de l'utilisateur connecté (optionnel)
            $table->string('user_name')->nullable(); // Nom de l'utilisateur
            $table->string('user_email')->nullable(); // Email de l'utilisateur
            $table->string('language', 10)->default('en'); // Langue de la conversation (en/ar)
            $table->enum('status', ['active', 'closed', 'transferred_to_admin'])->default('active'); // Statut de la conversation
            $table->timestamp('started_at')->useCurrent(); // Heure de début
            $table->timestamp('ended_at')->nullable(); // Heure de fin
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_conversations');
    }
};
