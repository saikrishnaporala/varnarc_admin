<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'user_id', 'active', 'country_id', 'gender', 'marital', 
        'spouse_complete_name', 'spouse_birthdate', 'children', 'place_of_birth', 
        'country_of_birth', 'birthday', 'ssnid', 'identification_id', 'passport_id', 
        'bank_account_id', 'permit_no', 'visa_no', 'visa_expire', 'additional_note', 
        'certificate', 'study_field', 'study_school', 'emergency_contact', 'emergency_phone', 
        'notes', 'barcode', 'pin', 'departure_reason', 'departure_description', 
        'department_id', 'job_id', 'company_id', 'address', 'street', 'street2', 
        'zip', 'city', 'state_id', 'work_phone', 'mobile_phone', 'work_email', 
        'work_location', 'photo', 'salary', 'parent_id', 'coach_id', 'currency_id', 
        'nationality', 'image', 'empid', 'dob', 'doj', 'department', 'designation', 
        'email', 'phone', 'username'
    ];
}
