<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('old_demands', function (Blueprint $table) {
            $table->integer('email_sent_count')->default(0)->after('outstanding');
        });
    }

    public function down()
    {
        Schema::table('old_demands', function (Blueprint $table) {
            $table->dropColumn('email_sent_count');
        });
    }
};
