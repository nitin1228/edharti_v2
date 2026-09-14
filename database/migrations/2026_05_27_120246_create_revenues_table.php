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
        Schema::create('revenues', function (Blueprint $table) {
            $table->id();
            $table->string('financial_year', 9); // 2026-2027
            $table->string('month', 20); // April, May, etc.
            $table->decimal('revenue_amount_without_ntrp', 15, 2)->default(0);
            $table->decimal('total_revenue_amount_pfms', 15, 2)->default(0);
            $table->timestamps();

            // Unique constraint to prevent duplicate entries
            $table->unique(['financial_year', 'month'], 'unique_financial_year_month');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('revenues');
    }
};
