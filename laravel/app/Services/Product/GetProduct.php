<?php

namespace App\Services\Product;

use App\Repository\Contracts\ProductInterface;

class GetProduct
{
    public function __construct(
        private ProductInterface $repository,
    ) {}

    public function execute($request)
    {

        $data = is_array($request) ? $request : $request->all();
        $product_type_id = $data['product_type_id'] ?? 0;
        $name = $data['name'] ?? '';

        $product = $this->repository->get($product_type_id, $name);
        return $product;
    }
}
