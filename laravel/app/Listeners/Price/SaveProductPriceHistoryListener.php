<?php

declare(strict_types=1);

namespace App\Listeners\Price;

use App\Events\Price\SaveProductPriceHistoryEvent;
use App\Jobs\Queue\Price\SaveProductPriceHistoryJob;

class SaveProductPriceHistoryListener
{
    public function __construct() {}

    public function handle(SaveProductPriceHistoryEvent $event): void
    {
        SaveProductPriceHistoryJob::dispatch(
            $event->productId,
            $event->productVariantId,
            $event->priceType,
            $event->oldPrice,
            $event->newPrice,
            $event->oldCostPrice,
            $event->newCostPrice,
            $event->oldProfitMargin,
            $event->newProfitMargin,
            $event->changeType,
            $event->userId,
            $event->ipAddress,
            $event->reason,
            $event->metadata,
            $event->effectiveAt,
        );
    }
}
