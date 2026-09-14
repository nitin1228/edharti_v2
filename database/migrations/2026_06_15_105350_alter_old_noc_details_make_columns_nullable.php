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
        Schema::table('old_noc_details', function (Blueprint $table) {
            $table->string('property_id', 100)->nullable()->change();
            $table->string('colony_code', 100)->nullable()->change();
            $table->string('colony_name', 255)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('old_noc_details', function (Blueprint $table) {
            $table->string('property_id', 100)->nullable(false)->change();
            $table->string('colony_code', 100)->nullable(false)->change();
            $table->string('colony_name', 255)->nullable(false)->change();
        });
    }
};