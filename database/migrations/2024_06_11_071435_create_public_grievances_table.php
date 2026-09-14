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
        Schema::create('public_grievances', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->bigInteger('mobile_number');
            $table->string('email');
            $table->integer('property_id');
            $table->string('colony_name');
            $table->string('address');
            $table->text('description');
            $table->enum('status', ['pending', 'resolved'])->default('pending');
            $table->timestamps();
            $table->unsignedBigInteger('updated_by')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('public_grievances');
    }
};
