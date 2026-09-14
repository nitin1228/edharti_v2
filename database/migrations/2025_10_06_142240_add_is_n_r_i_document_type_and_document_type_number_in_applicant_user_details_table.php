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
            $table->boolean('isIndian')->after('aadhar_card')->default(true);
            $table->string('documentType')->nullable()->comment('pion, ocin, passport')->after('isIndian');
            $table->string('documentTypeNumber')->nullable()->after('documentType');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applicant_user_details', function (Blueprint $table) {
            $table->dropColumn(['isIndian', 'documentType', 'documentTypeNumber']);
        });
    }
};
