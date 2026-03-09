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

    public function execute(array $request) : array
    {
        $parent_id = $request['id'];
        $name = $request['name'];
        $slug = $request['slug'];
        $description = $request['description'];
        $variantType = $request['variant_type'];

        $variantTypeMustBeValidRule = new VariantTypeMustBeValid($variantType);
        $variantTypeMustBeValidRule->validate();

        $parentProductType = $this->productTypeRepository->findProductTypeById($parent_id);

        if(empty($parentProductType)) {
            throw new ResourceNotFoundException('Parent id not found', ['There is no parent with id '.$parent_id.'.']);
        }

        $parentProductType = ArrayHelper::getFirstArrayFromList($parentProductType);

        dd($parentProductType);

        return [];
    }
}
