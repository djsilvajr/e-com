<?php

namespace App\Services;

use App\Services\Product\GetProductById;

class ProductService
{
    public function __construct(
        private GetProductById $getProductById,
    ) {}

    public function getProductById(int $id)
    {
        return $this->getProductById->execute($id);
    }
}
