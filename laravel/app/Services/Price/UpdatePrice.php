<?php

declare(strict_types=1);

namespace App\Services\Price;

use App\Events\Price\SaveProductPriceHistoryEvent;
use App\Exceptions\BusinessRuleException;
use App\Exceptions\ResourceNotFoundException;
use App\Repository\Contracts\PriceInterface;
use App\Repository\Contracts\ProductInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpdatePrice
{
    public function __construct(
        private PriceInterface $priceRepository,
        private ProductInterface $productRepository,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function execute(int $productId, array $data): array
    {
        $product = $this->productRepository->findById($productId);
        if (!$product) {
            throw new ResourceNotFoundException('Product not found');
        }

        $currency = $data['currency'] ?? null;
        if ($currency !== null && $currency !== 'BRL') {
            throw new BusinessRuleException('Currency must be BRL');
        }

        $price = $this->priceRepository->findByProductId($productId);
        if (!$price) {
            throw new ResourceNotFoundException('Price not found for this product');
        }

        $updated = $this->priceRepository->update($productId, $data);

        $this->dispatchPriceHistory($productId, $price, $updated);

        return $updated;
    }

    /**
     * @param  array<string, mixed>  $oldPrice
     * @param  array<string, mixed>  $newPrice
     */
    private function dispatchPriceHistory(int $productId, array $oldPrice, array $newPrice): void
    {
        event(new SaveProductPriceHistoryEvent(
            productId:        $productId,
            productVariantId: null,
            priceType:        'base',
            oldPrice:         (float) ($oldPrice['base_price'] ?? 0),
            newPrice:         (float) ($newPrice['base_price'] ?? 0),
            oldCostPrice:     isset($oldPrice['cost_price']) ? (float) $oldPrice['cost_price'] : null,
            newCostPrice:     isset($newPrice['cost_price']) ? (float) $newPrice['cost_price'] : null,
            oldProfitMargin:  isset($oldPrice['profit_margin']) ? (float) $oldPrice['profit_margin'] : null,
            newProfitMargin:  isset($newPrice['profit_margin']) ? (float) $newPrice['profit_margin'] : null,
            changeType:       'manual',
            userId:           Auth::id(),
            ipAddress:        request() instanceof Request ? request()->ip() : null,
            reason:           'Price updated',
        ));
    }
}
