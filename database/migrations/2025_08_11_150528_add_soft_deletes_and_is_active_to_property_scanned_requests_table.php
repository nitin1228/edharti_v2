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
        Schema::table('property_scanned_requests', function (Blueprint $table) {
            // Adds the 'deleted_at' column for Laravel SoftDeletes
            $table->softDeletes();

            // Adds is_active column (1 = active, 0 = inactive)
            $table->boolean('is_active')
                  ->default(1)
                  ->after('status'); // place it after 'status' column, adjust as needed
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('property_scanned_requests', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn('is_active');
        });
    }
};
