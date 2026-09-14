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
        Schema::table('newly_added_properties', function (Blueprint $table) {
            $table->integer('land_type')->nullable()->after('land_use_sub_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('newly_added_properties', function (Blueprint $table) {
            $table->dropColumn('land_type');
        });
    }
};
