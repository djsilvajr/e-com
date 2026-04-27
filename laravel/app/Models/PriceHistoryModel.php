<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceHistoryModel extends Model
{
    protected $table = 'price_histories';

    protected $guarded = [];

    protected $casts = [
        'old_price'           => 'decimal:2',
        'new_price'           => 'decimal:2',
        'old_cost_price'      => 'decimal:2',
        'new_cost_price'      => 'decimal:2',
        'old_profit_margin'   => 'decimal:2',
        'new_profit_margin'   => 'decimal:2',
        'price_difference'    => 'decimal:2',
        'percentage_change'   => 'decimal:2',
        'metadata'            => 'array',
        'changed_at'          => 'datetime',
        'effective_at'        => 'datetime',
    ];
}
