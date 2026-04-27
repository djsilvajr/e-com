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

class AddPrice
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
        if ($currency !== 'BRL') {
            throw new BusinessRuleException('Currency must be BRL');
        }

        $existing = $this->priceRepository->findByProductId($productId);
        if ($existing) {
            throw new BusinessRuleException('A price already exists for this product');
        }

        $data['product_id'] = $productId;

        $price = $this->priceRepository->create($data);

        $this->dispatchPriceHistory($productId, $price);

        return $price;
    }

    /**
     * @param  array<string, mixed>  $price
     */
    private function dispatchPriceHistory(int $productId, array $price): void
    {
        event(new SaveProductPriceHistoryEvent(
            productId:        $productId,
            productVariantId: null,
            priceType:        'base',
            oldPrice:         0.0,
            newPrice:         (float) ($price['base_price'] ?? 0),
            oldCostPrice:     null,
            newCostPrice:     isset($price['cost_price']) ? (float) $price['cost_price'] : null,
            oldProfitMargin:  null,
            newProfitMargin:  isset($price['profit_margin']) ? (float) $price['profit_margin'] : null,
            changeType:       'manual',
            userId:           Auth::id(),
            ipAddress:        request() instanceof Request ? request()->ip() : null,
            reason:           'Initial price registration',
        ));
    }
}
