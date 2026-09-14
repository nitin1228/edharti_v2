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
        Schema::table('user_registrations', function (Blueprint $table) {
            $table->dropColumn(['isNRI', 'passport_number', 'oci_card_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_registrations', function (Blueprint $table) {
            $table->boolean('isNRI')->after('aadhar_number')->default(false);
            $table->string('passport_number')->after('isNRI')->nullable();
            $table->string('oci_card_number')->after('passport_number')->nullable();
        });
    }
};
