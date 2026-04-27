<?php

declare(strict_types=1);

namespace App\Services\Price;

use App\Exceptions\InvalidParametersException;
use App\Repository\Contracts\PriceHistoryInterface;

class SaveProductPriceHistory
{
    private const ALLOWED_PRICE_TYPES = [
        'base',
        'variant_adjustment',
        'promotional',
        'cost',
    ];

    private const ALLOWED_CHANGE_TYPES = [
        'manual',
        'automatic',
        'bulk',
        'promotional',
        'cost_adjustment',
        'competitor',
        'seasonal',
        'clearance',
    ];

    public function __construct(
        private PriceHistoryInterface $priceHistoryRepository,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function execute(array $data): array
    {
        $productId = (int) ($data['product_id'] ?? 0);
        if ($productId <= 0) {
            throw new InvalidParametersException(
                'product_id is required',
                ['product_id' => 'product_id is required'],
            );
        }

        $priceType = (string) ($data['price_type'] ?? 'base');
        if (!in_array($priceType, self::ALLOWED_PRICE_TYPES, true)) {
            throw new InvalidParametersException(
                'price_type is invalid',
                ['price_type' => 'price_type is invalid'],
            );
        }

        $changeType = (string) ($data['change_type'] ?? 'manual');
        if (!in_array($changeType, self::ALLOWED_CHANGE_TYPES, true)) {
            throw new InvalidParametersException(
                'change_type is invalid',
                ['change_type' => 'change_type is invalid'],
            );
        }

        if (!array_key_exists('old_price', $data) || !array_key_exists('new_price', $data)) {
            throw new InvalidParametersException(
                'old_price and new_price are required',
                ['price' => 'old_price and new_price are required'],
            );
        }

        $oldPrice = (float) $data['old_price'];
        $newPrice = (float) $data['new_price'];

        if ($oldPrice < 0 || $newPrice < 0) {
            throw new InvalidParametersException(
                'prices must be greater than or equal to zero',
                ['price' => 'prices must be greater than or equal to zero'],
            );
        }

        $priceDifference  = round($newPrice - $oldPrice, 2);
        $percentageChange = $oldPrice > 0
            ? round((($newPrice - $oldPrice) / $oldPrice) * 100, 2)
            : null;

        $payload = [
            'product_id'         => $productId,
            'product_variant_id' => $this->nullableInt($data['product_variant_id'] ?? null),
            'price_type'         => $priceType,
            'old_price'          => $oldPrice,
            'new_price'          => $newPrice,
            'old_cost_price'     => $this->nullableFloat($data['old_cost_price'] ?? null),
            'new_cost_price'     => $this->nullableFloat($data['new_cost_price'] ?? null),
            'old_profit_margin'  => $this->nullableFloat($data['old_profit_margin'] ?? null),
            'new_profit_margin'  => $this->nullableFloat($data['new_profit_margin'] ?? null),
            'price_difference'   => $priceDifference,
            'percentage_change'  => $percentageChange,
            'change_type'        => $changeType,
            'user_id'            => $this->nullableInt($data['user_id'] ?? null),
            'ip_address'         => isset($data['ip_address']) ? (string) $data['ip_address'] : null,
            'reason'             => isset($data['reason']) ? (string) $data['reason'] : null,
            'metadata'           => is_array($data['metadata'] ?? null) ? $data['metadata'] : null,
            'changed_at'         => now(),
            'effective_at'       => $data['effective_at'] ?? null,
        ];

        return $this->priceHistoryRepository->create($payload);
    }

    private function nullableInt(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }

    private function nullableFloat(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (float) $value;
    }
}
