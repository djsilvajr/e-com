<?php

namespace App\Services\ProductVariant;

use App\Repository\Contracts\ProductVariantInterface;
use App\Repository\Contracts\ProductInterface;
use App\Exceptions\ResourceNotFoundException;

class GetProductVariant
{
    public function __construct(
        private ProductVariantInterface $productVariantRepository,
        private ProductInterface $productRepository,
    ) {}

    public function execute(int $productId, int $id): array
    {
        $product = $this->productRepository->findById($productId);
        if (!$product) {
            throw new ResourceNotFoundException('Product not found');
        }

        $variant = $this->productVariantRepository->findByProductIdAndId($productId, $id);
        if (!$variant) {
            throw new ResourceNotFoundException('Product variant not found');
        }

        return $variant;
    }
}
