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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('companyName', 45)->nullable();
            $table->string('leadImage', 45)->nullable();
            $table->string('title', 45)->nullable();
            $table->string('fname', 45)->nullable();
            $table->string('lname', 45)->nullable();
            $table->string('industryType', 45)->nullable();
            $table->string('leadSource', 45)->nullable();
            $table->string('lead_source_descr', 45)->nullable();
            $table->string('primaryEmail', 45)->nullable();
            $table->string('alternateEmail', 45)->nullable();
            $table->string('website', 45)->nullable();
            $table->string('skype', 45)->nullable();
            $table->string('phone_work', 45)->nullable();
            $table->string('phone_home', 45)->nullable();
            $table->string('phone_mobile', 45)->nullable();
            $table->string('phone_other', 45)->nullable();
            $table->unsignedInteger('assigned_to')->nullable();
            $table->string('partner', 100)->nullable();
            $table->string('reffered_by', 200)->nullable();
            $table->string('category', 45)->nullable();
            $table->string('department', 45)->nullable();
            $table->string('status', 45)->nullable();
            $table->string('status_descr', 45)->nullable();
            $table->string('primary_address', 45)->nullable();
            $table->string('primary_city', 45)->nullable();
            $table->string('primary_state', 45)->nullable();
            $table->string('primary_country', 45)->nullable();
            $table->string('primary_zip', 45)->nullable();
            $table->string('other_address', 45)->nullable();
            $table->string('other_city', 45)->nullable();
            $table->string('other_state', 45)->nullable();
            $table->string('other_country', 45)->nullable();
            $table->string('other_zip', 45)->nullable();
            $table->string('cby', 45)->nullable();
            $table->string('cdate', 45)->nullable();
            $table->string('cip', 45)->nullable();
            $table->string('mby', 45)->nullable();
            $table->string('mdate', 45)->nullable();
            $table->string('mip', 45)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
