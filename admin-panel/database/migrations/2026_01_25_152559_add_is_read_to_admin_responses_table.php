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
        Schema::table('admin_responses', function (Blueprint $table) {
            $table->boolean('is_read')->default(false)->after('response_attachments');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_responses', function (Blueprint $table) {
            $table->dropColumn('is_read');
        });
    }
};
