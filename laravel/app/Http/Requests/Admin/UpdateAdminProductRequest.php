<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UpdateAdminProductRequest extends FormRequest
{
    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'id'                     => ['required', 'integer', 'min:1'],
            'product_type_id'        => ['required', 'integer', 'min:1'],
            'name'                   => ['required', 'string', 'max:255'],
            'sku'                    => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'sku')->ignore($id),
            ],
            'description'            => ['required', 'string'],
            'short_description'      => ['required', 'string', 'max:255'],
            'brand'                  => ['nullable', 'string', 'max:255'],
            'model'                  => ['nullable', 'string', 'max:255'],
            'avg_weight'             => ['nullable', 'numeric', 'min:0'],
            'avg_dimensions'         => ['required', 'array'],
            'avg_dimensions.width'   => ['required', 'numeric', 'min:0'],
            'avg_dimensions.height'  => ['required', 'numeric', 'min:0'],
            'avg_dimensions.length'  => ['required', 'numeric', 'min:0'],
            'avg_dimensions.unit'    => ['required', 'string', 'max:10'],
            'total_stock'            => ['nullable', 'integer', 'min:0'],
            'min_stock'              => ['nullable', 'integer', 'min:0'],
            'meta_title'             => ['nullable', 'string', 'max:255'],
            'meta_description'       => ['nullable', 'string'],
            'active'                 => ['required', 'boolean'],
            'is_featured'            => ['required', 'boolean'],
            'is_new'                 => ['required', 'boolean'],
            'has_variants'           => ['required', 'boolean'],
            'available_at'           => ['required', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'sku.unique'                      => 'Já existe um produto com este SKU.',
            'product_type_id.required'        => 'Selecione um tipo de produto.',
            'name.required'                   => 'O nome é obrigatório.',
        ];
    }

    public function validationData(): array
    {
        $routeParameters = $this->route() ? $this->route()->parameters() : [];
        $payload = array_merge($this->all(), $routeParameters);

        foreach (['active', 'is_featured', 'is_new', 'has_variants'] as $flag) {
            if (array_key_exists($flag, $payload)) {
                $payload[$flag] = filter_var($payload[$flag], FILTER_VALIDATE_BOOLEAN);
            } else {
                $payload[$flag] = false;
            }
        }

        return $payload;
    }

    protected function failedValidation(Validator $validator): void
    {
        $id = $this->route('id');

        throw (new ValidationException($validator))
            ->redirectTo(route('admin.products.edit', ['id' => $id]));
    }

    public function authorize(): bool
    {
        return true;
    }
}
