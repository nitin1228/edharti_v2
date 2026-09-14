<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('user_registrations', function (Blueprint $table) {
            $table->softDeletes(); // adds nullable deleted_at TIMESTAMP
        });
    }

    public function down(): void
    {
        Schema::table('user_registrations', function (Blueprint $table) {
            $table->dropSoftDeletes(); // drops deleted_at
        });
    }
};
