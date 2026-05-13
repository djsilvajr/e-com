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
        $variantType   = (string) ($data['variant_type'] ?? '');
        $productTypeId = (int) ($data['product_type_id'] ?? 0);
        $name          = (string) ($data['name'] ?? '');

        // New API: search by variant_type (root category) or by a specific
        // product_type_id (sub-type). When neither is provided, return empty.
        if ($variantType !== '' || $productTypeId > 0) {
            return $this->repository->getFiltered(
                $variantType,
                $productTypeId > 0 ? $productTypeId : null,
                $name
            );
        }

        return [];
    }
}
