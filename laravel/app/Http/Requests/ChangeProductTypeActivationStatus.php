<?php

use App\Contracts\RequestValidationInterface;
use App\Helpers\Validator;

use App\Exceptions\InvalidParametersException;

class ChangeProductTypeActivationStatus implements RequestValidationInterface {

    public static function validate(array $credentials) : void {

        $id = $credentials['id'] ?? 0;
        $statusValidation = ($credentials['status'] === 'TRUE' || $credentials['status'] === 'FALSE') ? true : false;

        if(!Validator::positiveInt($id)) {
            throw new InvalidParametersException('Invalid parameters.', ['ID is not valid.']);
        }

        if(!$statusValidation) {
            throw new InvalidParametersException('Invalid parameters.', ['Status should be sent as "TRUE" or "FALSE".']);
        }

    }

}
