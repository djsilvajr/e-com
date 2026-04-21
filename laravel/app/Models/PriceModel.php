<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceModel extends Model
{
    protected $table = 'prices';
    protected $guarded = [];

    protected $casts = [
        'promotional_starts_at' => 'datetime',
        'promotional_ends_at' => 'datetime',
    ];
}
