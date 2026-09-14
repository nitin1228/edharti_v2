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
        Schema::create('application_extra_applicants', function (Blueprint $table) {
            $table->id();
            $table->string('application_no', 100)->index();
            $table->string('model_name', 100);

            $table->string('name');
            $table->string('relation')->nullable();
            $table->string('age')->nullable();

            $table->enum('gender', [
                'Male',
                'Female',
                'Other'
            ])->nullable();

            $table->string('share', 100);

            $table->timestamps();

            $table->index(['application_no', 'model_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_extra_applicants');
    }
};
