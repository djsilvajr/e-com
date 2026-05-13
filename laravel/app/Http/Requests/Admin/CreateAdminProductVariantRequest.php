<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use App\Enums\ProductVariantType;

class CreateAdminProductVariantRequest extends FormRequest
{
    public function rules(): array
    {
        $variantTypes = array_map(fn ($c) => $c->value, ProductVariantType::cases());

        return [
            'product_id'              => ['required', 'integer', 'min:1'],
            'sku'                     => ['required', 'string', 'max:255', 'unique:product_variants,sku'],
            'name'                    => ['required', 'string', 'max:255'],
            'barcode'                 => ['required', 'string', 'max:255', 'unique:product_variants,barcode'],
            'variant_type'            => ['required', 'string', 'in:' . implode(',', $variantTypes)],
            'price_adjustment'        => ['nullable', 'numeric'],
            'stock'                   => ['nullable', 'integer', 'min:0'],
            'reserved_stock'          => ['nullable', 'integer', 'min:0'],
            'min_stock'               => ['nullable', 'integer', 'min:0'],
            'weight'                  => ['nullable', 'numeric', 'min:0'],
            'dimensions'              => ['nullable', 'array'],
            'dimensions.altura'       => ['nullable', 'numeric'],
            'dimensions.largura'      => ['nullable', 'numeric'],
            'dimensions.profundidade' => ['nullable', 'numeric'],
            'image_url'               => ['nullable', 'string', 'max:1024'],
            'order'                   => ['nullable', 'integer', 'min:0'],
            'active'                  => ['required', 'boolean'],
            'is_default'              => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'sku.required'        => 'O SKU da variante é obrigatório.',
            'sku.unique'          => 'Já existe uma variante com este SKU.',
            'barcode.required'    => 'O código de barras é obrigatório.',
            'barcode.unique'      => 'Já existe uma variante com este código de barras.',
            'variant_type.in'     => 'O tipo de variante informado é inválido.',
            'name.required'       => 'O nome da variante é obrigatório.',
        ];
    }

    public function validationData(): array
    {
        $routeParameters = $this->route() ? $this->route()->parameters() : [];
        $payload = array_merge($this->all(), $routeParameters);

        if (isset($routeParameters['product_id'])) {
            $payload['product_id'] = (int) $routeParameters['product_id'];
        }

        foreach (['active', 'is_default'] as $flag) {
            $payload[$flag] = array_key_exists($flag, $payload)
                ? filter_var($payload[$flag], FILTER_VALIDATE_BOOLEAN)
                : false;
        }

        return $payload;
    }

    protected function failedValidation(Validator $validator): void
    {
        $productId = (int) ($this->route('product_id') ?? 0);

        throw (new ValidationException($validator))
            ->redirectTo(route('admin.products.variants.create', ['product_id' => $productId]));
    }

    public function authorize(): bool
    {
        return true;
    }
}
