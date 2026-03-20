<?php

namespace App\Services;

use App\Services\Product\GetProductById;
use App\Services\Product\CreateProduct;
use App\Services\Product\UpdateProduct;

class ProductService
{
    public function __construct(
        private GetProductById $getProductById,
        private CreateProduct $createProduct,
        private UpdateProduct $updateProduct,
    ) {}

    public function getProductById(int $id)
    {
        return $this->getProductById->execute($id);
    }

    public function createProduct($request)
    {
        return $this->createProduct->execute($request);
    }

    public function updateProduct($id, $request)
    {
        return $this->updateProduct->execute($id, $request);
    }
}
