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
            $table->string('action_taken_by')->nullable()->after('status')->comment('Which roles can approve user registration'); // Change bigInt to string
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_registrations', function (Blueprint $table) {
            $table->dropColumn('action_taken_by');
        });
    }
};
