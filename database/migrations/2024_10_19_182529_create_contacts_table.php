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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->nullable();
            $table->string('image', 100)->nullable();
            $table->string('email', 200)->nullable();
            $table->string('score', 200)->nullable();
            $table->string('phone', 20)->nullable();
            
            $table->unsignedBigInteger('company')->nullable(); 
            $table->foreign('company')->references('id')->on('companies')->onDelete('set null');
    
            $table->string('tags', 200)->nullable();
            $table->string('mobile', 20)->nullable();
            $table->string('leadsource', 100)->nullable();
            $table->string('home_phone', 20)->nullable();
            $table->string('dept', 100)->nullable();
            $table->string('fax', 20)->nullable();
            $table->date('dob')->nullable();
            $table->string('assistant', 200)->nullable();
            $table->string('reportsto', 200)->nullable();
            $table->string('assistant_phone', 20)->nullable();
            $table->string('sec_email', 200)->nullable();
            $table->string('emailopt', 10)->nullable();
            $table->string('donotcall', 10)->nullable();
            $table->string('reference', 100)->nullable();
            $table->string('assignedto', 100)->nullable();
            $table->string('notifyowner', 100)->nullable();
            $table->string('portaluser', 100)->nullable();
            $table->date('supportstartdate')->nullable();
            $table->date('supportenddate')->nullable();
            $table->longText('m_street')->nullable();
            $table->string('m_city', 100)->nullable();
            $table->string('m_state', 100)->nullable();
            $table->string('m_zip', 10)->nullable();
            $table->string('m_country', 100)->nullable();
            $table->longText('o_street')->nullable();
            $table->string('o_city', 100)->nullable();
            $table->string('o_state', 100)->nullable();
            $table->string('o_zip', 100)->nullable();
            $table->string('o_country', 100)->nullable();
            $table->longText('description')->nullable();
            $table->string('createdby', 100)->nullable();
            $table->string('createdip', 100)->nullable();
            $table->string('modifiedby', 100)->nullable();
            $table->string('modifiedip', 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
