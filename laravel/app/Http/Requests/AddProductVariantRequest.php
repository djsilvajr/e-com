<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;
use App\Exceptions\InvalidParametersException;
use App\Enums\ProductVariantType;

class AddProductVariantRequest extends FormRequest
{
    public function rules(): array
    {
        $variantTypes = array_map(fn($c) => $c->value, ProductVariantType::cases());

        return [
            'product_id'       => ['required', 'integer', 'min:1'],
            'sku'              => ['required', 'string', 'max:255', 'unique:product_variants,sku'],
            'name'             => ['required', 'string', 'max:255'],
            'barcode'          => ['required', 'string', 'max:255', 'unique:product_variants,barcode'],
            'variant_type'     => ['required', 'string', Rule::in($variantTypes)],
            'price_adjustment' => ['nullable', 'numeric', 'decimal:0,2'],
            'stock'            => ['nullable', 'integer', 'min:0'],
            'reserved_stock'   => ['nullable', 'integer', 'min:0'],
            'min_stock'        => ['nullable', 'integer', 'min:0'],
            'weight'           => ['nullable', 'numeric', 'decimal:0,2'],
            'dimensions'       => ['nullable', 'array'],
            'dimensions.altura'       => ['required_with:dimensions', 'numeric'],
            'dimensions.largura'      => ['required_with:dimensions', 'numeric'],
            'dimensions.profundidade' => ['required_with:dimensions', 'numeric'],
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
