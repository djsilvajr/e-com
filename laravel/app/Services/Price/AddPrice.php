<?php

namespace App\Services\Price;

use App\Repository\Contracts\PriceInterface;
use App\Repository\Contracts\ProductInterface;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\BusinessRuleException;

class AddPrice
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
        if ($currency !== 'BRL') {
            throw new BusinessRuleException('Currency must be BRL');
        }

        $existing = $this->priceRepository->findByProductId($productId);
        if ($existing) {
            throw new BusinessRuleException('A price already exists for this product');
        }

        $data['product_id'] = $productId;

        $price = $this->priceRepository->create($data);

        return $price;
    }
}
