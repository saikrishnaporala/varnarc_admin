<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('drive_files', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('size'); // pending, imported, failed
            $table->string('table_name')->nullable()->after('status');
        });
    }

    public function down()
    {
        Schema::table('drive_files', function (Blueprint $table) {
            $table->dropColumn(['status', 'table_name']);
        });
    }
};
