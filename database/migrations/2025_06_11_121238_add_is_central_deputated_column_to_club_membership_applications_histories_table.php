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
        Schema::table('club_membership_applications_histories', function (Blueprint $table) {
            $table->boolean('is_central_deputated')->default(0)->after('new_club_type');
            $table->boolean('new_is_central_deputated')->default(0)->after('is_central_deputated');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('club_membership_applications_histories', function (Blueprint $table) {
            $table->dropColumn('is_central_deputated');
            $table->dropColumn('new_is_central_deputated');
        });
    }
};
