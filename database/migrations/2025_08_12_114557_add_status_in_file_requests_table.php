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
            $table->enum('status', ['pending', 'accepted', 'rejected'])
                ->default('pending')
                ->after('request_remark')
                ->comment('Status of the file request: pending, accepted, or rejected');
            $table->text('rejection_reason')
                ->nullable()
                ->after('status')
                ->comment('Reason for rejection of the file request, if applicable');
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
