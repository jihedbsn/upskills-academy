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
        Schema::create('chat_bot_responses', function (Blueprint $table) {
            $table->id();
            $table->string('keyword')->nullable(); // Mot-clé pour déclencher la réponse
            $table->text('question_en'); // Question en anglais
            $table->text('question_ar'); // Question en arabe
            $table->text('response_en'); // Réponse en anglais
            $table->text('response_ar'); // Réponse en arabe
            $table->string('category')->default('general'); // Catégorie de la réponse
            $table->integer('priority')->default(1); // Priorité de la réponse
            $table->boolean('is_active')->default(true); // Si la réponse est active
            $table->timestamps();
            
            $table->index(['keyword', 'category']);
            $table->index(['is_active', 'priority']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_bot_responses');
    }
};
