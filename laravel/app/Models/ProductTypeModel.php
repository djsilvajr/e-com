<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\ProductVariantType;


class ProductTypeModel extends Model
{
    use SoftDeletes;

    protected $table = 'product_types';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'variant_type',
        'order',
        'icon',
        'image_url',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'order' => 'integer',
        'parent_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'variant_type' => ProductVariantType::class,
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeOrderedActive($query)
    {
        return $query->where('active', true)->orderBy('order');
    }

    public function scopeOfVariant(Builder $query, string|array $types): Builder
    {
        $types = (array) $types; // aceita string ou array
        return $query->whereIn('variant_type', $types);
    }
}
