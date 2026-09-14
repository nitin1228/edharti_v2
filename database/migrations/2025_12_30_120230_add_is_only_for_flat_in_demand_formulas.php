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
        Schema::table('demand_formulas', function (Blueprint $table) {
            $table->boolean('is_only_for_flat')->default(false)->after('for_allotment_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('demand_formulas', function (Blueprint $table) {
            $table->dropColumn('is_only_for_flat');
        });
    }
};
