<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    // Specify the table if the model name doesn't match the table name
    protected $table = 'leads';

    // Enable mass assignment for the specified fields
    protected $fillable = [
        'name', 
        'company', 
        'score', 
        'phone', 
        'email', 
        'location', 
        'leadImage', 
        'industryType', 
        'leadSource', 
        'lead_source_descr', 
        'alternateEmail', 
        'website', 
        'skype', 
        'phone_work', 
        'phone_home', 
        'phone_mobile', 
        'assigned_to', 
        'partner', 
        'reffered_by', 
        'category', 
        'department', 
        'status', 
        'tags', 
        'primary_address', 
        'primary_city', 
        'primary_state', 
        'primary_country', 
        'primary_zip', 
        'other_address', 
        'other_city', 
        'other_state', 
        'other_country', 
        'other_zip', 
        'cby', 
        'cip', 
        'mby', 
        'mip'
    ];

    // If you want to customize the date fields
    protected $dates = [
        'created_at', 
        'updated_at'
    ];
}
