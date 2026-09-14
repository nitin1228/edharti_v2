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
        Schema::create('application_document_details', function (Blueprint $table) {
            $table->id();
            $table->string('application_no');
            $table->string('model_name')->nullable();
            $table->string('document_name')->nullable();
            $table->date('document_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_document_details');
    }
};
