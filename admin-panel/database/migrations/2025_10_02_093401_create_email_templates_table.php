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
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Nom unique du template
            $table->string('type'); // Type: quiz, stage, video_exam, student_success, custom
            $table->string('status'); // Status: validated, rejected, approved, etc.
            $table->string('subject'); // Sujet de l'email
            $table->longText('content'); // Contenu HTML du template
            $table->json('variables')->nullable(); // Variables disponibles pour ce template
            $table->text('description')->nullable(); // Description du template
            $table->boolean('is_active')->default(true); // Template actif ou non
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_templates');
    }
};
