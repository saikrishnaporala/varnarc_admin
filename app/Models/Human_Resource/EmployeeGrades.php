<?php

namespace App\Models\Human_Resource;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeGrades extends Model
{
    use HasFactory;

    protected $fillable = [
        'grade_name', 'min_salary', 'max_salary', 'benefits', 'promotion_criteria'
    ];
}
