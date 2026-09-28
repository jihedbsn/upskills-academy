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
        Schema::create('stage_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->enum('document_type', ['request_letter', 'evaluation_form']); // خطاب الطلب or استمارة التقييم
            $table->string('file_name'); // Original file name
            $table->string('file_path'); // Path to file in storage
            $table->string('mime_type')->nullable(); // pdf, docx, etc.
            $table->integer('file_size')->nullable(); // Size in bytes
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->unique(['product_id', 'document_type']); // One document of each type per course

            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stage_documents');
    }
};
