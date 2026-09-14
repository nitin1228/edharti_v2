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
        Schema::table('file_requests', function (Blueprint $table) {
            $table->integer('colony_id')->after('current_section')->nullable();
            $table->string('block')->after('colony_id')->nullable();
            $table->string('plot')->after('block')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('file_requests', function (Blueprint $table) {
            //
        });
    }
};
