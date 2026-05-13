<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

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

    public static function findByVariantType(
        int $typeId = 0,
        string $name = ''
    ): Collection {
        return self::query()
            ->when($typeId, function ($query) use ($typeId) {
                $query->where('product_type_id', $typeId);
            })
            ->when($name !== '', function ($query) use ($name) {
                $query->where('name', 'LIKE', "%{$name}%");
            })
            ->get();
    }
}
