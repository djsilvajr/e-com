<?php

namespace App\Http\Requests;
use App\Contracts\RequestValidationInterface;
use App\Exceptions\InvalidParametersException;
use App\Helpers\Validator;

class CreateChildProductType implements RequestValidationInterface
{
    public static function validate(array $credentials) : void
    {
        $id = $credentials['id'] ?? 0;
        $name = $credentials['name'] ?? '';
        $slug = $credentials['slug'] ?? '';
        $description = $credentials['description'] ?? '';

        if(!Validator::positiveInt($id)) {
            throw new InvalidParametersException("Error Processing Request", ['Invalid id parameter'], 400);
        }

        if(empty($name)) {
            throw new InvalidParametersException("Error Processing Request", ['Invalid name parameter. "name" can not be empty.']);
        }

        if(strlen($name) > 255) {
            throw new InvalidParametersException("Error Processing Request", ['Invalid name parameter. Maximum length is 255.']);
        }

        if(empty($slug)) {
            throw new InvalidParametersException("Error Processing Request", ['Invalid slug parameter. "slug" can not be empty.']);
        }

        if(strlen($slug) > 255) {
            throw new InvalidParametersException("Error Processing Request", ['Invalid slug parameter. Maximum length is 255.']);
        }
    }
}
