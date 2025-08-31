<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrDepartment extends Model
{
    use HasFactory, SoftDeletes;

    // Specify the table name if it doesn't match the model name
    protected $table = 'hr_departments';

    // Define fillable fields for mass assignment
    protected $fillable = [
        'department_name',
        'complete_name',
        'active',
        'company_id',
        'parent_id',
        'manager_id',
        'note',
    ];

    // Define relationships if applicable
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function parentDepartment()
    {
        return $this->belongsTo(HrDepartment::class, 'parent_id');
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }
}
