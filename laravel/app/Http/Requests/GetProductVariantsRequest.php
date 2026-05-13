<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Exceptions\InvalidParametersException;

class GetProductVariantsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'min:1'],
        ];
    }

    public function validationData(): array
    {
        $routeParameters = $this->route() ? $this->route()->parameters() : [];
        return array_merge($this->all(), $routeParameters);
    }

    protected function failedValidation(Validator $validator): void
    {
        $allMessages = $validator->errors()->messages();

        $formatted = [];
        foreach ($allMessages as $field => $messages) {
            $formatted[$field] = isset($messages[0]) ? $messages[0] : implode(' ', $messages);
        }

        $firstField = count($formatted) ? array_key_first($formatted) : null;
        $firstMessage = $firstField ? $formatted[$firstField] : 'Invalid parameters.';

        throw new InvalidParametersException('Invalid parameters.', [
            'first_error_field' => $firstField,
            'first_error_message' => $firstMessage,
            'errors' => $formatted,
        ]);
    }

    public function authorize(): bool
    {
        return true;
    }
}
