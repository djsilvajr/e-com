<?php

namespace App\Services\ProductVariant;

use App\Repository\Contracts\ProductVariantInterface;
use App\Repository\Contracts\ProductInterface;
use App\Exceptions\ResourceNotFoundException;

class ListProductVariants
{
    public function __construct(
        private ProductVariantInterface $productVariantRepository,
        private ProductInterface $productRepository,
    ) {}

    /**
     * Returns all variants of a given product. The list MAY be empty
     * but the product MUST exist — otherwise it throws.
     *
     * @return array<int, array<string, mixed>>
     */
    public function execute(int $productId): array
    {
        $product = $this->productRepository->findById($productId);
        if (!$product) {
            throw new ResourceNotFoundException('Product not found');
        }

        return $this->productVariantRepository->findAllByProductId($productId);
    }
}
