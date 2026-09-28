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
        Schema::create('student_stage_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('file1')->nullable(); // Premier fichier (image ou PDF)
            $table->string('file2')->nullable(); // Deuxième fichier (image ou PDF)
            $table->text('description')->nullable(); // Description optionnelle
            $table->boolean('is_valid')->default(false); // Validation par l'admin
            $table->text('admin_notes')->nullable(); // Notes de l'admin
            $table->timestamp('submitted_at')->nullable(); // Date de soumission
            $table->timestamp('validated_at')->nullable(); // Date de validation
            $table->timestamps();
            
            // Index pour éviter les doublons
            $table->unique(['student_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_stage_courses');
    }
};