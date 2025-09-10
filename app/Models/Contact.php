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
    ];

    protected $casts = [
        'tags' => 'array', // 👈 MUST be here
    ];
}
