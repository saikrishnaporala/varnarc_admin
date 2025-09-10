<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'status', 'visibility', 'publish_at',
        'category', 'tags', 'short_description', 'manufacturer',
        'stocks', 'price', 'discount', 'orders', 'image', 'gallery',
        'meta_title', 'meta_keywords', 'meta_description'
    ];

    protected $casts = [
        'gallery' => 'array',
        'publish_at' => 'datetime',
    ];
}
