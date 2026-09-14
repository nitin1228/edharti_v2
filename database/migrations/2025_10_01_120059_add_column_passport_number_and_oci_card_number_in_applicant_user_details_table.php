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
        Schema::table('applicant_user_details', function (Blueprint $table) {
            $table->string('pan_card')->nullable()->change();
            $table->string('aadhar_card')->nullable()->change();
            $table->boolean('isNRI')->after('aadhar_card')->default(false);
            $table->string('passport_number')->after('isNRI')->nullable();
            $table->string('oci_card_number')->after('passport_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applicant_user_details', function (Blueprint $table) {
            $table->string('pan_card')->nullable(false)->change();
            $table->string('aadhar_card')->nullable(false)->change();
            $table->dropColumn('isNRI');
            $table->dropColumn('passport_number');
            $table->dropColumn('oci_card_number');
        });
    }
};
