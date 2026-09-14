<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreatePropertyScannedRequestsTable extends Migration
{
    public function up()
    {
        // ✅ Pre-fetch the default ID from items table for 'SCAN_NEW'
        $defaultStatusId = DB::table('items')->where('item_code', 'SCAN_NEW')->value('id') ?? 0;

        Schema::create('property_scanned_requests', function (Blueprint $table) use ($defaultStatusId) {
            $table->id();
            $table->string('unique_id')->unique();
            $table->unsignedBigInteger('property_master_id');
            $table->unsignedBigInteger('splited_property_detail_id')->nullable()->default(null);
            $table->unsignedBigInteger('flat_id')->nullable()->default(null);
            $table->unsignedBigInteger('old_property_id');
            $table->unsignedBigInteger('colony_id');
            $table->unsignedBigInteger('application_id')->nullable()->default(null);
            $table->unsignedBigInteger('record_id')->nullable()->default(null);

            // ✅ Use static default value
            $table->unsignedBigInteger('status')->default($defaultStatusId);

            $table->string('file_location')->nullable()->default(null);
            $table->string('file_request_path')->nullable()->default(null);
            $table->string('file_return_path')->nullable()->default(null);
            $table->text('remarks')->nullable()->default(null);

            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable()->default(null);
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);
        });
    }

    public function down()
    {
        Schema::dropIfExists('property_scanned_requests');
    }
}
