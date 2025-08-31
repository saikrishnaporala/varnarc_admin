<?php

namespace App\Models\Human_Resource;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HRDesignation extends Model
{
    // Specify the table name if it's different from the model name in plural
    protected $table = 'hr_designations';

    // Define fillable attributes for mass assignment
    protected $fillable = [
        'department_id',
        'designation_name',
        'complete_name',
        'active',
        'company_id',
        'parent_id',
        'manager_id',
        'note',
    ];

    // Add relationships if necessary
    public function department()
    {
        return $this->belongsTo(HRDepartment::class, 'department_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
