<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FieldMapping extends Model
{
    use HasFactory;

    // Table name (optional if following Laravel convention)
    protected $table = 'field_mappings';

    // Allow mass assignment
    protected $fillable = [
        'from_table',
        'to_table',
        'mapping',
    ];

    // Cast mapping column as JSON automatically
    protected $casts = [
        'mapping' => 'array',
    ];
}
