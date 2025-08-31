<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    // Specify the table if it's not the plural form of the model name
    protected $table = 'companies';

    // Fillable fields
    protected $fillable = [
        'name',
        'image_src',
        'owner',
        'industry_type',
        'company_type',
        'star_value',
        'location',
        'employee',
        'website',
        'contact_email',
        'contact_phone',
        'since',
        'pisd',
        'misd',
        'tin',
        'cst',
        'address1',
        'address2',
        'area',
        'city',
        'state',
        'country',
        'landmark',
        'zip',
        'pname',
        'pdesignation',
        'pdepartment',
        'ppisd',
        'pphone',
        'pmisd',
        'pmobile',
        'pemail',
        'sname',
        'sdesignation',
        'sdepartment',
        'spisd',
        'sphone',
        'smisd',
        'smobile',
        'semail',
        'active',
        'comment'
    ];

    public function contacts() {
        return $this->hasMany(Contact::class);
    }

    public function branches() {
        return $this->hasMany(Branch::class);
    }
}
