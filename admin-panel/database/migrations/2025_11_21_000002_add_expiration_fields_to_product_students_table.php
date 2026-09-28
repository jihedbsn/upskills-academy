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
        Schema::table('product_students', function (Blueprint $table) {
            $table->datetime('expiration_date')->nullable()->after('access_granted_at')
                ->comment('When course access expires. NULL = lifetime access');
            $table->boolean('is_expired')->default(false)->after('expiration_date')
                ->comment('Whether course access has expired');
            $table->integer('extension_count')->default(0)->after('is_expired')
                ->comment('Number of times access has been extended');
            $table->datetime('last_expiration_reminder')->nullable()->after('extension_count')
                ->comment('Last time expiration reminder was sent');
            $table->integer('last_reminder_interval')->nullable()->after('last_expiration_reminder')
                ->comment('Days before expiration when last reminded (30,20,10,5,2,1)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_students', function (Blueprint $table) {
            $table->dropColumn([
                'expiration_date',
                'is_expired',
                'extension_count',
                'last_expiration_reminder',
                'last_reminder_interval'
            ]);
        });
    }
};
