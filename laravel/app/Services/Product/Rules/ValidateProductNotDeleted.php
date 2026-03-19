<?php

namespace App\Services\Product\Rules;

use App\Exceptions\BusinessRuleException;

class ValidateProductNotDeleted
{
    public function validate($product)
    {
        if ($product['deleted_at'] !== null) {
            throw new BusinessRuleException('Product not available.');
        }
    }
}
