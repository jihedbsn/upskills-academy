<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('practical_exam_attempts', function (Blueprint $table) {
            $table->unsignedBigInteger('training_case_file_id')->nullable()->after('training_case_id');
            $table->foreign('training_case_file_id')->references('id')->on('training_case_files')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('practical_exam_attempts', function (Blueprint $table) {
            $table->dropForeign(['training_case_file_id']);
            $table->dropColumn('training_case_file_id');
        });
    }
};
