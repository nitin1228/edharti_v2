<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('club_membership_applications', function (Blueprint $table) {
            $table->boolean('is_central_deputated')->default(0)->after('club_type');
        });
    }

    public function down()
    {
        Schema::table('club_membership_applications', function (Blueprint $table) {
            $table->dropColumn('is_central_deputated');
        });
    }
};

