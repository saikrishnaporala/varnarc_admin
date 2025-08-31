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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->boolean('active')->default(true);
            $table->unsignedBigInteger('country_id')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->boolean('marital')->nullable();
            $table->string('spouse_complete_name')->nullable();
            $table->date('spouse_birthdate')->nullable();
            $table->integer('children')->nullable();
            $table->string('place_of_birth')->nullable();
            $table->unsignedBigInteger('country_of_birth')->nullable();
            $table->date('birthday')->nullable();
            $table->string('ssnid')->nullable();
            $table->string('identification_id')->nullable();
            $table->string('passport_id')->nullable();
            $table->string('bank_account_id')->nullable();
            $table->string('permit_no')->nullable();
            $table->string('visa_no')->nullable();
            $table->date('visa_expire')->nullable();
            $table->text('additional_note')->nullable();
            $table->string('certificate')->nullable();
            $table->string('study_field')->nullable();
            $table->string('study_school')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->string('emergency_phone')->nullable();
            $table->text('notes')->nullable();
            $table->string('barcode')->nullable();
            $table->string('pin')->nullable();
            $table->string('departure_reason')->nullable();
            $table->text('departure_description')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('job_id')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->string('address')->nullable();
            $table->string('street')->nullable();
            $table->string('street2')->nullable();
            $table->string('zip')->nullable();
            $table->string('city')->nullable();
            $table->unsignedBigInteger('state_id')->nullable();
            $table->string('work_phone')->nullable();
            $table->string('mobile_phone')->nullable();
            $table->string('work_email')->nullable();
            $table->string('work_location')->nullable();
            $table->string('photo')->nullable();
            $table->decimal('salary', 10, 2)->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->unsignedBigInteger('coach_id')->nullable();
            $table->unsignedBigInteger('currency_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->string('nationality')->nullable();
            $table->string('image')->nullable();
            $table->string('empid')->nullable();
            $table->date('dob')->nullable();
            $table->date('doj')->nullable();
            $table->string('department')->nullable();
            $table->string('designation')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('username')->nullable();

            // Foreign keys can be defined here if necessary
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
