<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('property_scanned_files', function (Blueprint $table) {
            // document_name: allow null, default null
            $table->string('document_name')
                  ->nullable()
                  ->default(null)
                  ->change();

            // old_property_file_name: not nullable
            $table->string('old_property_file_name')
                  ->nullable(false)
                  ->change();
        });
    }

    public function down(): void
    {
        Schema::table('property_scanned_files', function (Blueprint $table) {
            // revert changes (adjust if your original schema differs)
            $table->string('document_name')
                  ->nullable(false)
                  ->change();

            $table->string('old_property_file_name')
                  ->nullable()
                  ->change();
        });
    }
};
