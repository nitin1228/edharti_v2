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
        Schema::table('flats', function (Blueprint $table) {
            $table->string('address')->nullable()->after('builder_developer_name');
            $table->string('email')->nullable()->after('address');
            $table->string('phone')->nullable()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('flats', function (Blueprint $table) {
            $table->dropColumn('address');
            $table->dropColumn('email');
            $table->dropColumn('phone');
        });
    }
};
