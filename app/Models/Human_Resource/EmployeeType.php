<?php

namespace App\Models\Human_Resource;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeType extends Model
{
    use HasFactory;

    protected $fillable = [
        'type_name',
        'description',
    ];
}
