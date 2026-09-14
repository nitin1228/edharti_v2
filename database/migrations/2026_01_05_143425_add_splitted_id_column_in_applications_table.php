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
        Schema::table('temp_substitution_mutation', function (Blueprint $table) {
            $table->integer('splitted_id')->nullable()->after('property_master_id');
        });
        Schema::table('mutation_applications', function (Blueprint $table) {
            $table->integer('splitted_id')->nullable()->after('property_master_id');
        });
        Schema::table('noc_applications', function (Blueprint $table) {
            $table->integer('splitted_id')->nullable()->after('property_master_id');
        });
        Schema::table('temp_nocs', function (Blueprint $table) {
            $table->integer('splitted_id')->nullable()->after('property_master_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('temp_substitution_mutation', function (Blueprint $table) {
            $table->dropColumn('splitted_id');
        });
        Schema::table('mutation_applications', function (Blueprint $table) {
            $table->dropColumn('splitted_id');
        });
        Schema::table('noc_applications', function (Blueprint $table) {
            $table->dropColumn('splitted_id');
        });
        Schema::table('temp_nocs', function (Blueprint $table) {
            $table->dropColumn('splitted_id');
        });
    }
};
