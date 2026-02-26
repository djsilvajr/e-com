<?php

namespace App\Services\ProductType\Ensures;

use App\Repository\Contracts\ProductTypeInterface;
use App\Exceptions\ResourceNotFoundException;

class EnsureProductTypeExist
{
    public function __construct(
        private ProductTypeInterface $productTypeRepository
    ) {}

    public function validate(int $id): void
    {
        $productType = $this->productTypeRepository->findProductTypeById($id);

        if (!$productType) {
            throw new ResourceNotFoundException("Product type not found.");
        }
    }
}
