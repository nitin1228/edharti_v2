<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('temp_scanned_files_property_id', function (Blueprint $table) {
            $table->id();
            $table->integer('property_id');       // int(11)
            $table->string('location')->nullable(); // varchar, default NULL

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('temp_scanned_files_property_id');
    }
};
