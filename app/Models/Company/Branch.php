<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'branch_name',
        'address',
        'city',
        'state',
        'zip_code',
        'country',
        'phone_number',
        'email',
        'manager_name',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
