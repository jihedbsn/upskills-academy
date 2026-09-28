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
        Schema::table('email_logs', function (Blueprint $table) {
            $table->longText('body')->nullable()->after('subject');
            $table->string('tracking_token', 64)->nullable()->unique()->after('body');
            $table->timestamp('read_at')->nullable()->after('tracking_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_logs', function (Blueprint $table) {
            $table->dropColumn(['body', 'tracking_token', 'read_at']);
        });
    }
};
