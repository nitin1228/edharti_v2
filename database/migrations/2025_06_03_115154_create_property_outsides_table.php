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
        Schema::create('property_outsides', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('property_master_id')->nullable();
            $table->integer('old_property_id')->nullable();
            $table->string('file_no')->nullable(); // Who provided the land
            $table->unsignedBigInteger('state_id');
            $table->unsignedBigInteger('city_id');
            $table->text('address');
            $table->string('land_type')->nullable(); // Who provided the land
            $table->string('status')->nullable(); // Lease Hold, Free Hold, Outside Delhi, Unalloted etc..
            $table->decimal('area', 15, 2);
            $table->string('received_from'); // Who provided the land
            $table->date('custody_date'); // When taken into custody
            // $table->unsignedBigInteger('land_use')->nullable();
            $table->string('land_use')->nullable(); // Who provided the land
            $table->boolean('user_by_any_department')->default(false); // True = occupied illegally
            $table->string('department')->nullable(); // Intended use (e.g., Institutional)
            $table->boolean('encroached')->default(false); // True = occupied illegally
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->foreign('property_master_id')->references('id')->on('property_masters')->onDelete('set null');
            $table->foreign('state_id')->references('id')->on('states')->onDelete('cascade');
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
            // $table->foreign('land_use')->references('id')->on('items')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_outsides');
    }
};
