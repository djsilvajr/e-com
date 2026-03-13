<?php

namespace App\Http\Requests;

use App\Contracts\RequestValidationInterface;
use App\Helpers\Validator;
use App\Exceptions\InvalidParametersException;
use App\Enums\ProductTypeActivationStatus;

class ChangeProductTypeActivationStatus implements RequestValidationInterface {

    public static function validate(array $credentials) : void {

        $id = $credentials['id'] ?? 0;
        $status = strtoupper($credentials['status'] ?? '');

        if(!Validator::positiveInt($id)) {
            throw new InvalidParametersException('Invalid parameters.', ['ID is not valid.']);
        }

        if(!ProductTypeActivationStatus::tryFrom($status)) {
            throw new InvalidParametersException('Invalid parameters.', ['Status should be sent as "TRUE" or "FALSE".']);
        }

    }

}
