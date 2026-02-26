<?php

namespace App\Services\ProductType;

use App\Exceptions\ResourceNotFoundException;
use App\Repository\Contracts\ProductTypeInterface;

use App\Services\ProductType\Ensures\EnsureProductTypeExist;

class GetProductTypeById
{
    public function __construct(
        private ProductTypeInterface $productTypeRepository,
        private EnsureProductTypeExist $ensureProductTypeExist
    ) {

    }

    public function execute(int $id) : array
    {
        $this->ensureProductTypeExist->validate($id);

        $productType = $this->productTypeRepository->findProductTypeById($id);
        $productTypeArray = (array) current($productType);
        return $productTypeArray;
    }
}
