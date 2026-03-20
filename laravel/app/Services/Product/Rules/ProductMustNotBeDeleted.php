<?php

namespace App\Services\Product\Rules;

use App\Exceptions\BusinessRuleException;

class ProductMustNotBeDeleted
{
    public function __construct() {}

    public function validate(array $product): void
    {
        if (isset($product['deleted_at']) && !empty($product['deleted_at'])) {
            throw new BusinessRuleException('Product is deleted and cannot be updated.', []);
        }
    }
}