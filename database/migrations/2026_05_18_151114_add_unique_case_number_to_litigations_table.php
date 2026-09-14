<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('litigations', function (Blueprint $table) {
            $table->unique('case_number');
        });
    }

    public function down()
    {
        Schema::table('litigations', function (Blueprint $table) {
            $table->dropUnique(['case_number']);
        });
    }
};
