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
        Schema::create('case_hearings', function (Blueprint $table) {

            $table->id();

            $table->foreignId('litigation_id')
                ->constrained('litigations')
                ->cascadeOnDelete();

            $table->date('ldoh')->nullable();

            $table->text('ldoh_hearing_details')->nullable();

            $table->date('ndoh')->nullable();

            $table->text('remarks')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_hearings');
    }
};
