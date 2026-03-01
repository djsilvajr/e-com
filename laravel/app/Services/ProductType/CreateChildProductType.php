<?php

namespace App\Services\ProductType;

use App\Repository\ProductTypeRepository;
use App\Services\ProductType\Rules\VariantTypeMustBeValid;

class CreateChildProductType {

    public function __construct(
        private ProductTypeRepository $productTypeRepository,
    ) {}

    public function execute(array $data) : array
    {
        $id = $data['id'];
        $name = $data['name'];
        $slug = $data['slug'];
        $description = $data['description'] ?? '';
        $variantType = $data['variant_type'] ?? '';

        $variantTypeMustBeValidRule = new VariantTypeMustBeValid($variantType);
        $variantTypeMustBeValidRule->validate();


        $allTypesRelatedByVariantType = $this->productTypeRepository->getAllTypesByVariantType($variantType);
        print_r($allTypesRelatedByVariantType);
        die;

        return [];
    }
}
