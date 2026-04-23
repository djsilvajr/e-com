<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariantModel extends Model
{
    protected $table = 'product_variants';
    protected $guarded = [];

    protected $casts = [
        'dimensions' => 'array',
        'price_adjustment' => 'decimal:2',
        'weight' => 'decimal:2',
        'stock' => 'integer',
        'reserved_stock' => 'integer',
        'min_stock' => 'integer',
        'order' => 'integer',
        'active' => 'boolean',
        'is_default' => 'boolean',
    ];
}
