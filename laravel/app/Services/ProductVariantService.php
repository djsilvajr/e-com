<?php

namespace App\Services;

use App\Services\ProductVariant\GetProductVariant;
use App\Services\ProductVariant\ListProductVariants;
use App\Services\ProductVariant\AddProductVariant;
use App\Services\ProductVariant\UpdateProductVariant;
use App\Services\ProductVariant\DeleteProductVariant;

class ProductVariantService
{
    public function __construct(
        private GetProductVariant $getProductVariant,
        private ListProductVariants $listProductVariants,
        private AddProductVariant $addProductVariant,
        private UpdateProductVariant $updateProductVariant,
        private DeleteProductVariant $deleteProductVariant,
    ) {}

    public function get(int $productId, int $id): array
    {
        return $this->getProductVariant->execute($productId, $id);
    }

    public function list(int $productId): array
    {
        return $this->listProductVariants->execute($productId);
    }

    public function add(int $productId, array $data): array
    {
        return $this->addProductVariant->execute($productId, $data);
    }

    public function update(int $productId, int $id, array $data): array
    {
        return $this->updateProductVariant->execute($productId, $id, $data);
    }

    public function delete(int $productId, int $id): bool
    {
        return $this->deleteProductVariant->execute($productId, $id);
    }
}
