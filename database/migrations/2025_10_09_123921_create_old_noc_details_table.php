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
        Schema::create('old_noc_details', function (Blueprint $table) {
            $table->id();
            $table->string('application_number', 100)->nullable();
            $table->string('property_id', 100);
            $table->string('colony_code', 100);
            $table->string('colony_name', 255);
            $table->string('block_number', 100)->nullable();
            $table->string('property_number', 100)->nullable();
            $table->string('known_as', 255)->nullable();
            $table->string('section', 255)->nullable();
            $table->string('file_num')->nullable();
            $table->date('noc_issued_date')->nullable();
            $table->date('dispatch_date')->nullable();

            // API link (exact URL from API)
            $table->text('file_link');

            // NEW: local storage path & status flag
            $table->string('file_path', 1024)->nullable();
            $table->unsignedTinyInteger('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('old_noc_details');
    }
};
