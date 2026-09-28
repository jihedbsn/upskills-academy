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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('nom');
            $table->text('description')->nullable();
            $table->decimal('valeur', 5, 2); // Valeur en pourcentage (ex: 15.50)
            $table->date('date_debut');
            $table->date('date_fin');
            $table->integer('usage_limit')->nullable(); // Limite d'utilisation
            $table->integer('usage_count')->default(0); // Nombre d'utilisations
            $table->boolean('is_active')->default(true);
            $table->string('type')->default('percentage'); // percentage ou fixed
            $table->decimal('montant_minimum', 10, 2)->nullable(); // Montant minimum pour utiliser le coupon
            $table->timestamps();
            
            $table->index(['date_debut', 'date_fin']);
            $table->index(['is_active']);
            $table->index(['code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};