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
        Schema::create('product_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('products_id')->constrained('products')->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->string('metakeyword')->nullable();
            $table->text('content')->nullable();
            $table->string('metadescription')->nullable();
            $table->string('type', 50)->nullable();
            $table->string('type_course', 20)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_types');
    }
};


