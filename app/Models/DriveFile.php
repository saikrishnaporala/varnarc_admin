<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriveFile extends Model
{
    use HasFactory;

    protected $table = 'drive_files';

    protected $fillable = [
        'file_id',
        'name',
        'mime',
        'url',
    ];
}
