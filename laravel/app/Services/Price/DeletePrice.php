<?php

namespace App\Services\Price;

use App\Repository\Contracts\PriceInterface;
use App\Repository\Contracts\ProductInterface;
use App\Exceptions\ResourceNotFoundException;

class DeletePrice
{
    public function __construct(
        private PriceInterface $priceRepository,
        private ProductInterface $productRepository,
    ) {}

    public function execute(int $productId): bool
    {
        $product = $this->productRepository->findById($productId);
        if (!$product) {
            throw new ResourceNotFoundException('Product not found');
        }

        $price = $this->priceRepository->findByProductId($productId);
        if (!$price) {
            throw new ResourceNotFoundException('Price not found for this product');
        }

        return $this->priceRepository->deleteByProductId($productId);
    }
}
