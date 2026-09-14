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
            $table->string('correct_property_id')->nullable()->after('suggested_property_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('newly_added_properties', function (Blueprint $table) {
            $table->dropColumn('correct_property_id');
        });
    }
};
