<?php

namespace App\Services\Product;

use App\Repository\Contracts\ProductInterface;
use App\Exceptions\ResourceNotFoundException;
use App\Services\Product\Rules\ValidateProductNotDeleted;

class GetProductById
{
    public function __construct(
        private ProductInterface $repository,
    ) {}

    public function execute(int $id)
    {
        $product = $this->repository->findById($id);
        if (!$product) {
            throw new ResourceNotFoundException('Produto não encontrado');
        }
        (new ValidateProductNotDeleted())->validate($product);
        return $product;
    }
}
