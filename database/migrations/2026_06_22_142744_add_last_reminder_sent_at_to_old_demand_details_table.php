<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('old_demands', function (Blueprint $table) {
            $table->timestamp('last_reminder_sent_at')
                ->nullable()
                ->after('email_sent_count'); // adjust position as needed
        });
    }

    public function down()
    {
        Schema::table('old_demands', function (Blueprint $table) {
            $table->dropColumn('last_reminder_sent_at');
        });
    }
};
