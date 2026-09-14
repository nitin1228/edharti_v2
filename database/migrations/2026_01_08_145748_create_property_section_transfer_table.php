<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_section_transfer', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('old_property_id');
            $table->string('old_section_code');
            $table->string('new_section_code');
            $table->string('property_transfer_document')->nullable();
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_section_transfer');
    }
};
