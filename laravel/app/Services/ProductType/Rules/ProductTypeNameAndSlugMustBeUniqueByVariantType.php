<?php

namespace App\Services\ProductType\Rules;

use App\Exceptions\BusinessRuleException;

class ProductTypeNameAndSlugMustBeUniqueByVariantType
{
    public function __construct(
        private string $name,
        private string $slug,
        private array $existingProductType,
    ) {}

    public function validate(): void
    {
        if ($this->existingProductType['name'] === $this->name) {
            throw new BusinessRuleException(
                'Duplicate name',
                ["A product type with name '{$this->name}' already exists for this variant type."]
            );
        }

        if ($this->existingProductType['slug'] === $this->slug) {
            throw new BusinessRuleException(
                'Duplicate slug',
                ["A product type with slug '{$this->slug}' already exists for this variant type."]
            );
        }
    }
}
