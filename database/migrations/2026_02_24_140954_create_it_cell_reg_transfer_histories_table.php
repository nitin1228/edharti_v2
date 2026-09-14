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
        Schema::create('it_cell_reg_transfer_histories', function (Blueprint $table) {
            $table->id();

            $table->string('reg_app_no')->nullable();

            $table->string('manual_locality')->nullable();
            $table->string('manual_block')->nullable();
            $table->string('manual_plot')->nullable();
            $table->string('manual_flat')->nullable();
            $table->string('manual_known_as')->nullable();

            $table->integer('transfer_to_locality')->nullable();
            $table->string('transfer_to_block')->nullable();
            $table->string('transfer_to_plot')->nullable();
            $table->string('transfer_to_flat')->nullable();
            $table->string('transfer_to_known_as')->nullable();

            $table->string('old_property_id')->nullable();
            $table->string('section_code')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('it_cell_reg_transfer_histories');
    }
};

