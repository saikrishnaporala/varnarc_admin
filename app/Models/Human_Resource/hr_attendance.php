<?php

namespace App\Models\Human_Resource;

use Illuminate\Database\Eloquent\Model;

class hr_attendance extends Model
{
    protected $table = 'attendance';
    protected $fillable = ['employee_id', 'check_in','check_out'];
}
