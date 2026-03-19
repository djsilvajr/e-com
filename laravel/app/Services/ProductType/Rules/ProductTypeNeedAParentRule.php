<?php

namespace App\Services\ProductType\Rules;

use App\Exceptions\BusinessRuleException;
use App\Exceptions\DuplicatedValueException;

class ProductTypeNeedAParentRule
{
    public function __construct(

    ) {}

    public function validate($productType): void
    {
        if($productType['parent_id'] === null) {
            throw new BusinessRuleException('Product type must have a parent', ['product_type_id' => $productType['id']]);
        }
    }
}
