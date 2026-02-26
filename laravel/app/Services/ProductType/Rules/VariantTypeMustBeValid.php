<?php


namespace App\Services\ProductType\Rules;

use App\Enums\ProductVariantType;
use App\Exceptions\InvalidParametersException;

class VariantTypeMustBeValid {

    private string $variantType;

    public function __construct(
        string $variantType
    ){
        $this->variantType = $variantType;
    }

    public function validate () {
        $variantTypeValidation = ProductVariantType::tryFrom(
            $this->variantType
        );

        if(!$variantTypeValidation) {
            throw new InvalidParametersException('Variant Type Rule Exception.', ['The variant send does not match our variants.']);
        }
    }
}
