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
        Schema::create('litigations', function (Blueprint $table) {

            $table->id();

            $table->string('unique_case_id', 15)->unique();

            $table->string('property_id')->nullable();
            $table->string('property_known_as')->nullable();

            $table->string('case_number');
            $table->string('associated_case_number')->nullable();

            $table->string('case_type');

            $table->string('judicial_authority');
            $table->string('court_location');

            $table->text('case_brief');

            $table->string('section');
            $table->string('section_counsel');

            /*
            |--------------------------------------------------------------------------
            | JSON GROUPS
            |--------------------------------------------------------------------------
            */

            // Multiple parties
            $table->json('parties');

            // Multiple counsels
            $table->json('counsels');

            $table->text('section_remarks')->nullable();

            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('litigations');
    }
};
