<?php

declare(strict_types=1);

namespace App\Events\Price;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SaveProductPriceHistoryEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param  array<string, mixed>|null  $metadata
     */
    public function __construct(
        public int $productId,
        public ?int $productVariantId,
        public string $priceType,
        public float $oldPrice,
        public float $newPrice,
        public ?float $oldCostPrice = null,
        public ?float $newCostPrice = null,
        public ?float $oldProfitMargin = null,
        public ?float $newProfitMargin = null,
        public string $changeType = 'manual',
        public ?int $userId = null,
        public ?string $ipAddress = null,
        public ?string $reason = null,
        public ?array $metadata = null,
        public ?string $effectiveAt = null,
    ) {}
}
