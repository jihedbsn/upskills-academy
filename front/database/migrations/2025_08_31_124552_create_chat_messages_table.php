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
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conversation_id'); // ID de la conversation
            $table->enum('sender_type', ['user', 'bot']); // Type d'expéditeur
            $table->text('message'); // Contenu du message
            $table->json('metadata')->nullable(); // Métadonnées supplémentaires
            $table->timestamp('sent_at')->useCurrent(); // Heure d'envoi
            $table->timestamps();
            
            $table->foreign('conversation_id')->references('id')->on('chat_conversations')->onDelete('cascade');
            $table->index(['conversation_id', 'sent_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};
