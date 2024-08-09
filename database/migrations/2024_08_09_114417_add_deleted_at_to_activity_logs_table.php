<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeletedAtToActivityLogsTable extends Migration
{
    public function up()
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->softDeletes(); // Bu satır 'deleted_at' sütununu ekler
        });
    }

    public function down()
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropSoftDeletes(); // 'deleted_at' sütununu kaldırır
        });
    }
}

