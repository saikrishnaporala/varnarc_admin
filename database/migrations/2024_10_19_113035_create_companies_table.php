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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->nullable();
            $table->string('image_src', 255)->nullable();
            $table->string('owner', 255)->nullable();
            $table->string('industry_type', 255)->nullable();
            $table->string('company_type', 255)->nullable();
            $table->string('star_value', 255)->nullable();
            $table->string('location', 255)->nullable();
            $table->string('employee', 255)->nullable();
            $table->string('website', 255)->nullable();
            $table->string('contact_email', 255)->nullable();
            $table->string('contact_phone', 45)->nullable();
            $table->string('since', 255)->nullable();
            $table->string('pisd', 45)->nullable();
            $table->string('misd', 45)->nullable();
            $table->string('tin', 45)->nullable();
            $table->string('cst', 45)->nullable();
            $table->longText('address1')->nullable();
            $table->longText('address2')->nullable();
            $table->string('area', 45)->nullable();
            $table->string('city', 45)->nullable();
            $table->string('state', 45)->nullable();
            $table->string('country', 45)->nullable();
            $table->longText('landmark')->nullable();
            $table->string('zip', 45)->nullable();
            $table->string('pname', 45)->nullable();
            $table->string('pdesignation', 45)->nullable();
            $table->string('pdepartment', 45)->nullable();
            $table->string('ppisd', 45)->nullable();
            $table->string('pphone', 45)->nullable();
            $table->string('pmisd', 45)->nullable();
            $table->string('pmobile', 45)->nullable();
            $table->string('pemail', 60)->nullable();
            $table->string('sname', 45)->nullable();
            $table->string('sdesignation', 45)->nullable();
            $table->string('sdepartment', 45)->nullable();
            $table->string('spisd', 45)->nullable();
            $table->string('sphone', 45)->nullable();
            $table->string('smisd', 45)->nullable();
            $table->string('smobile', 45)->nullable();
            $table->string('semail', 60)->nullable();
            $table->string('active', 45)->nullable();
            $table->longText('comment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
