<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('property_scanned_files', function (Blueprint $table) {
            // Add nullable FK to users; set NULL if the user is deleted
            $table->foreignId('updated_by')
                  ->nullable()
                  ->after('updated_at')
                  ->constrained('users')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('property_scanned_files', function (Blueprint $table) {
            $table->dropForeign(['updated_by']);
            $table->dropColumn('updated_by');
        });
    }
};
