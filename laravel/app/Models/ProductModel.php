<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductModel extends Model
{
    protected $table = 'products';
    protected $guarded = [];

    protected $casts = [
        'attributes' => 'array',
        'avg_dimensions' => 'array',
        'meta_keywords' => 'array',
        'available_at' => 'datetime',
    ];
}
