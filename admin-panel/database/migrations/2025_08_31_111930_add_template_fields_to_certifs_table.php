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
        Schema::table('certifs', function (Blueprint $table) {
            $table->json('template_data')->nullable()->after('file_url');
            $table->enum('orientation', ['vertical', 'horizontal'])->default('vertical')->after('template_data');
            $table->boolean('is_active')->default(true)->after('orientation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('certifs', function (Blueprint $table) {
            $table->dropColumn(['template_data', 'orientation', 'is_active']);
        });
    }
};
