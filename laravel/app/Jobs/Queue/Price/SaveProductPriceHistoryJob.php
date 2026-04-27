<?php

declare(strict_types=1);

namespace App\Jobs\Queue\Price;

use App\Services\Price\SaveProductPriceHistory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SaveProductPriceHistoryJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 5;
    public int $timeout = 15;

    /**
     * @param  array<string, mixed>|null  $metadata
     */
    public function __construct(
        private int $productId,
        private ?int $productVariantId,
        private string $priceType,
        private float $oldPrice,
        private float $newPrice,
        private ?float $oldCostPrice,
        private ?float $newCostPrice,
        private ?float $oldProfitMargin,
        private ?float $newProfitMargin,
        private string $changeType,
        private ?int $userId,
        private ?string $ipAddress,
        private ?string $reason,
        private ?array $metadata,
        private ?string $effectiveAt,
    ) {
        $this->onQueue('savePriceHistory');
    }

    /**
     * @return array<int, int>
     */
    public function backoff(): array
    {
        return [10, 30, 60];
    }

    public function handle(SaveProductPriceHistory $saveProductPriceHistory): void
    {
        try {
            $saveProductPriceHistory->execute([
                'product_id'         => $this->productId,
                'product_variant_id' => $this->productVariantId,
                'price_type'         => $this->priceType,
                'old_price'          => $this->oldPrice,
                'new_price'          => $this->newPrice,
                'old_cost_price'     => $this->oldCostPrice,
                'new_cost_price'     => $this->newCostPrice,
                'old_profit_margin'  => $this->oldProfitMargin,
                'new_profit_margin'  => $this->newProfitMargin,
                'change_type'        => $this->changeType,
                'user_id'            => $this->userId,
                'ip_address'         => $this->ipAddress,
                'reason'             => $this->reason,
                'metadata'           => $this->metadata,
                'effective_at'       => $this->effectiveAt,
            ]);
        } catch (\Throwable $e) {
            Log::error('Error saving product price history', [
                'job'        => self::class,
                'product_id' => $this->productId,
                'error'      => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
