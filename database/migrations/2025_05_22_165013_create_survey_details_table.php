<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('survey_details', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedBigInteger('survey_id');
            $table->string('property_id');
            $table->string('surveyor_name');
            $table->string('lop_plot')->nullable();
            $table->string('lop_block')->nullable();
            $table->unsignedBigInteger('colony_id'); 
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->date('surveyed_at');
            $table->timestamps(); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_details');
    }
};

