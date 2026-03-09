<?php

namespace App\Services\ProductType;

use App\Repository\ProductTypeRepository;
use App\Services\ProductType\Rules\VariantTypeMustBeValid;
use App\Helpers\ArrayHelper;
use App\Exceptions\ResourceNotFoundException;

class CreateChildProductType {

    public function __construct(
        private ProductTypeRepository $productTypeRepository,
    ) {}

    public function execute(array $data) : array
    {
        $parent_id = $data['id'];
        $name = $data['name'];
        $slug = $data['slug'];
        $description = $data['description'] ?? '';
        $variantType = $data['variant_type'] ?? '';

        // Variant Type validate type
        $variantTypeMustBeValidRule = new VariantTypeMustBeValid($variantType);
        $variantTypeMustBeValidRule->validate();

        // Get Parent related to the new type
        $parentProductType = $this->productTypeRepository->findProductTypeById($parent_id);

        if(empty($parentProductType)) {
            throw new ResourceNotFoundException('Parent id not found', ['There is no parent with id '.$parent_id.'.']);
        }

        $parentProductType = ArrayHelper::getFirstArrayFromList($parentProductType);

        dd($parentProductType);

        // Get All children to make validation with the new one
        $allTypesRelatedByVariantType = $this->productTypeRepository->getAllTypesByVariantType($variantType);
        print_r($allTypesRelatedByVariantType);
        die;

        return [];
    }
}
