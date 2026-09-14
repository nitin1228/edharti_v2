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
        Schema::table('file_requests', function (Blueprint $table) {
            $table->string('requisition_file')->nullable()->after('plot');
            $table->string('returned_file_to_record')->nullable()->after('requisition_file');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('file_requests', function (Blueprint $table) {
            //
        });
    }
};
