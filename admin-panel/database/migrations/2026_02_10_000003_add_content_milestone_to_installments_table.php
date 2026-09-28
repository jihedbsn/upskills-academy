<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add content milestone tracking to installments table:
     * - content_milestone_id: Links this installment to the content it unlocks
     * - late_fee: Late fee accumulated specifically for this installment
     * - installment_number: Ordinal position (1st, 2nd, 3rd...)
     */
    public function up(): void
    {
        Schema::table('installments', function (Blueprint $table) {
            $table->foreignId('content_milestone_id')->nullable()->after('notes')
                  ->constrained('content_milestones')->nullOnDelete();
            $table->decimal('late_fee', 10, 2)->default(0)->after('content_milestone_id');
            $table->unsignedInteger('installment_number')->default(0)->after('late_fee');
        });
    }

    public function down(): void
    {
        Schema::table('installments', function (Blueprint $table) {
            $table->dropForeign(['content_milestone_id']);
            $table->dropColumn(['content_milestone_id', 'late_fee', 'installment_number']);
        });
    }
};
