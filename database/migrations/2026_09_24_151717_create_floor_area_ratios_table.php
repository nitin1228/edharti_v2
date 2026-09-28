<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('floor_area_ratios', function (Blueprint $table) {
            $table->id();

            // Period for which this FAR rule is applicable
            $table->date('effective_from')->nullable();
            $table->date('effective_to')->nullable();

            // Plot area range
            $table->decimal('area_from', 12, 2)->nullable();
            $table->decimal('area_to', 12, 2)->nullable();

            // sq_yd / sq_mt
            $table->string('area_unit', 20);

            $table->decimal('far', 8, 2);

            // Percentage, e.g. 75.00
            $table->decimal('ground_coverage', 5, 2)->nullable();

            $table->timestamps();

            $table->index([
                'effective_from',
                'effective_to',
                'area_from',
                'area_to'
            ], 'far_rule_lookup_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('floor_area_ratios');
    }
};
