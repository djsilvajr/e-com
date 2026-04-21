<?php

namespace App\Services\Price;

use App\Repository\Contracts\PriceInterface;
use App\Repository\Contracts\ProductInterface;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\BusinessRuleException;

class UpdatePrice
{
    public function __construct(
        private PriceInterface $priceRepository,
        private ProductInterface $productRepository,
    ) {}

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

        return $updated;
    }
}
