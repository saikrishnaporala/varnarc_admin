<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('drive_files', function (Blueprint $table) {
            $table->bigInteger('size')->nullable()->after('mime'); // file size in bytes
        });
    }

    public function down()
    {
        Schema::table('drive_files', function (Blueprint $table) {
            $table->dropColumn('size');
        });
    }
};
