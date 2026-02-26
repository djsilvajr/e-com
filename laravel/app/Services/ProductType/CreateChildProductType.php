<?php

namespace App\Services\ProductType;

use App\Repository\ProductTypeRepository;
use App\Services\ProductType\Rules\VariantTypeMustBeValid;

class CreateChildProductType {

    public function __construct(
        private ProductTypeRepository $productTypeRepository,
    ) {}

    public function execute(array $request) : array
    {
        $id = $request['id'];
        $name = $request['name'];
        $slug = $request['slug'];
        $description = $request['description'];
        $variantType = $request['variant_type'];

        $variantTypeMustBeValidRule = new VariantTypeMustBeValid($variantType);
        $variantTypeMustBeValidRule->validate();

        return [];
    }
}
