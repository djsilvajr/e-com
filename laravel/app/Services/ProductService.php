<?php

namespace App\Services;

use App\Services\Product\GetProductById;
use App\Services\Product\GetProduct;
use App\Services\Product\CreateProduct;
use App\Services\Product\UpdateProduct;
use App\Services\Product\DeleteProduct;

class ProductService
{
    public function __construct(
        private GetProductById $getProductById,
        private CreateProduct $createProduct,
        private UpdateProduct $updateProduct,
        private DeleteProduct $deleteProduct,
        private GetProduct $getProduct,
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

    public function deleteProduct($request)
    {
        return $this->deleteProduct->execute($request);
    }

    public function getProduct($request) {
        return $this->getProduct->execute($request);
    }
}
