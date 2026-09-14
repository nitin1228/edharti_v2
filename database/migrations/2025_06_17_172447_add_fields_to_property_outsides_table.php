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
            // Foreign key to present_custodians table
            $table->unsignedBigInteger('present_custodian')->nullable()->after('encroached');
            $table->foreign('present_custodian')->references('id')->on('present_custodians')->onDelete('set null');

            // Details text field
            $table->text('present_custodian_details')->nullable()->after('present_custodian');

            // Foreign key to items table
            $table->unsignedBigInteger('present_status')->nullable()->after('present_custodian_details');
            $table->foreign('present_status')->references('id')->on('items')->onDelete('set null');

            // Details text field
            $table->text('present_status_details')->nullable()->after('present_status');

            // Court case fields
            $table->boolean('court_case')->default(false)->after('present_status_details');
            $table->text('court_case_details')->nullable()->after('court_case');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('property_outsides', function (Blueprint $table) {
            $table->dropForeign(['present_custodian']);
            $table->dropColumn('present_custodian');
            $table->dropColumn('present_custodian_details');

            $table->dropForeign(['present_status']);
            $table->dropColumn('present_status');
            $table->dropColumn('present_status_details');

            $table->dropColumn('court_case');
            $table->dropColumn('court_case_details');
        });
    }
};
