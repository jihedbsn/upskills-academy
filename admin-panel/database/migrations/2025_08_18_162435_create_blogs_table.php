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
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->string('titre_ar');
            $table->string('titre_en');
            $table->string('meta_title_ar');
            $table->string('meta_title_en');
            $table->text('description_ar');
            $table->text('description_en');
            $table->text('description_short_ar')->nullable();
            $table->text('description_short_en')->nullable();
            $table->string('slug')->unique();
            $table->string('image')->nullable();
            $table->string('author_ar')->nullable();
            $table->string('author_en')->nullable();
            $table->date('published_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('views_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
