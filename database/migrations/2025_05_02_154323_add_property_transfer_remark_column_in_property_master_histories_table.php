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
        Schema::table('property_master_histories', function (Blueprint $table) {
            $table->longText('property_transfer_remark')->after('new_is_active')->nullable();
            $table->longText('new_property_transfer_remark')->after('property_transfer_remark')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('property_master_histories', function (Blueprint $table) {
            $table->dropColumn('property_transfer_remark');
            $table->dropColumn('new_property_transfer_remark');
        });
    }
};
