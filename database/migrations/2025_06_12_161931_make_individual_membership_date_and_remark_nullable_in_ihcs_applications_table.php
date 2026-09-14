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
        Schema::table('ihcs_applications', function (Blueprint $table) {
            $table->string('individual_membership_date_and_remark')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ihcs_applications', function (Blueprint $table) {
            $table->string('individual_membership_date_and_remark')->nullable(false)->change();
        });
    }
};
