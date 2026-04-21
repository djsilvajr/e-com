<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Exceptions\InvalidParametersException;

class UpdatePriceRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'product_id'            => ['required', 'integer', 'min:1'],
            'base_price'            => ['sometimes', 'numeric', 'decimal:0,2', 'min:0'],
            'cost_price'            => ['nullable', 'numeric', 'decimal:0,2', 'min:0'],
            'profit_margin'         => ['nullable', 'numeric', 'decimal:0,2', 'min:0'],
            'promotional_price'     => ['nullable', 'numeric', 'decimal:0,2', 'min:0'],
            'promotional_starts_at' => ['nullable', 'date'],
            'promotional_ends_at'   => ['nullable', 'date'],
            'compare_at_price'      => ['nullable', 'numeric', 'decimal:0,2', 'min:0'],
            'currency'              => ['sometimes', 'string', 'regex:/^BRL$/'],
            'tax_rate'              => ['nullable', 'numeric', 'decimal:0,2', 'min:0'],
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
