<?php

// app/Models/Contact.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'company_name',
        'designation',
        'email',
        'phone',
        'lead_score',
        'tags',
        'industrytype',
        'current_employer',
        'current_salary',
        'education',
        'key_skills',
        'date_of_birth',
        'age',
        'gender',
        'address1',
        'address2',
        'address3',
        'city',
        'state',
        'country',
        'pincode',
        'category',
        'sub_category',
    ];

    protected $casts = [
        'tags' => 'array', // 👈 MUST be here
    ];
}
