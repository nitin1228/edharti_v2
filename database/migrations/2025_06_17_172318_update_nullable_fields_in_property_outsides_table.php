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
        Schema::table('property_outsides', function (Blueprint $table) {
            $table->string('received_from')->nullable()->change();
            $table->date('custody_date')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('property_outsides', function (Blueprint $table) {
            $table->string('received_from')->nullable(false)->change();
            $table->date('custody_date')->nullable(false)->change();
        });
    }
};
