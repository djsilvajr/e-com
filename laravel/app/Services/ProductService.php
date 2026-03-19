<?php

namespace App\Services;

use App\Services\Product\GetProductById;
use App\Services\Product\CreateProduct;

class ProductService
{
    public function __construct(
        private GetProductById $getProductById,
        private CreateProduct $createProduct,
    ) {}

    public function getProductById(int $id)
    {
        return $this->getProductById->execute($id);
    }

    public function createProduct($request)
    {
        return $this->createProduct->execute($request);
    }
}
