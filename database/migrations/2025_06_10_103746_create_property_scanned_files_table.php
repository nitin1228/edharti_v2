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
        Schema::create('property_scanned_files', function (Blueprint $table) {
            $table->id();
            $table->integer('property_master_id');
            $table->integer('splited_property_detail_id')->nullable();
            $table->integer('flat_id')->nullable();
            $table->string('colony_name')->nullable();
            $table->integer('old_property_id');
            $table->string('document_name');
            $table->string('document_path')->nullable();
            $table->boolean('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_scanned_files');
    }
};
