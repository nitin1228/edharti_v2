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
        Schema::table('newly_added_properties', function (Blueprint $table) {
            // Add deleted_at column for soft deletes
            $table->softDeletes();
            
            // If you want to add an index for better query performance
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('newly_added_properties', function (Blueprint $table) {
            // Drop the deleted_at column and its index
            $table->dropSoftDeletes();
            $table->dropIndex(['deleted_at']);
        });
    }
};