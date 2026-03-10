<?php

namespace App\Services\ProductType;

use App\Repository\ProductTypeRepository;
use App\Services\ProductType\Rules\VariantTypeMustBeValid;
use App\Helpers\ArrayHelper;
use App\Exceptions\ResourceNotFoundException;
use App\Services\ProductType\Rules\ProductTypeNameAndSlugMustBeUniqueByVariantType;

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

        // === VariantType ===
        // Variant Type validate type
        $variantTypeMustBeValidRule = new VariantTypeMustBeValid($variantType);
        $variantTypeMustBeValidRule->validate();

        // === ParentProductType ===
        // Get Parent related to the new type
        $parentProductType = $this->productTypeRepository->findProductTypeById($parent_id);

        if(empty($parentProductType)) {
            throw new ResourceNotFoundException('Parent id not found', ['There is no parent with id '.$parent_id.'.']);
        }

        $parentProductType = ArrayHelper::getFirstArrayFromList($parentProductType);
        // Validate against parent
        $this->validateNameAndSlugUniqueness($name, $slug, [$parentProductType]);


        // === Children Based on ParentProductType and his variants
        // Get All children to make validation with the new one
        $allTypesRelatedByVariantType = $this->productTypeRepository->findChildProductTypesById($parent_id);
        // Validate against siblings (children of the same variant)
        if(!empty($allTypesRelatedByVariantType)) {
            $this->validateNameAndSlugUniqueness($name, $slug, ArrayHelper::convertStdObjectArrayToSimpleArray($allTypesRelatedByVariantType));
        }

        $creation = $this->productTypeRepository->insertVariantType($name, $slug, $description, $parent_id, $variantType);
        // Response
        return $creation;
    }

    private function validateNameAndSlugUniqueness(string $name, string $slug, array $existingProductTypes): void
    {
        foreach ($existingProductTypes as $existingProductType) {
            $rule = new ProductTypeNameAndSlugMustBeUniqueByVariantType($name, $slug, $existingProductType);
            $rule->validate();
        }
    }
}
