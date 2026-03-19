<?php

namespace App\Services\ProductType\Rules;

use App\Exceptions\ResourceNotFoundException;

class ProductTypeMustNotBeDeleted
{
    public function validate($productType): void
    {
        if (!empty($productType['deleted_at'])) {
            throw new ResourceNotFoundException('Product type is deleted', ['The product type with id ' . $productType['id'] . ' has been deleted.']);
        }
    }
}
