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
        Schema::create('accueil_chiffres', function (Blueprint $table) {
            $table->id();
            $table->integer('coach_ready')->default(0);
            $table->string('icone_coach_ready')->nullable();
            $table->integer('book_of_the_academy')->default(0);
            $table->string('icone_book_of_the_academy')->nullable();
            $table->integer('registered_student')->default(0);
            $table->string('icone_registered_student')->nullable();
            $table->integer('training_program')->default(0);
            $table->string('icone_training_program')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accueil_chiffres');
    }
};
