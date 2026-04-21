<?php

namespace App\Services\Price;

use App\Repository\Contracts\PriceInterface;
use App\Repository\Contracts\ProductInterface;
use App\Exceptions\ResourceNotFoundException;

class GetPrice
{
    public function __construct(
        private PriceInterface $priceRepository,
        private ProductInterface $productRepository,
    ) {}

    public function execute(int $productId): array
    {
        $product = $this->productRepository->findById($productId);
        if (!$product) {
            throw new ResourceNotFoundException('Product not found');
        }

        $price = $this->priceRepository->findByProductId($productId);
        if (!$price) {
            throw new ResourceNotFoundException('Price not found for this product');
        }

        return $price;
    }
}
