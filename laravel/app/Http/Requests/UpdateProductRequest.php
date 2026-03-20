<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;
use App\Exceptions\InvalidParametersException;

class UpdateProductRequest extends FormRequest
{
    public function rules()
    {
        $id = $this->route('id');

        return [
            'id' => ['required', 'integer', 'min:1'],
            'product_type_id' => ['required', 'integer', 'min:1'],
            'name' => ['required', 'string', 'max:255'],
            'sku' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'sku')->ignore($id),
            ],
            'description' => ['required', 'string'],
            'short_description' => ['required', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'attributes' => ['nullable', 'array'],
            'avg_weight' => ['nullable', 'numeric'],
            'avg_dimensions' => ['required', 'array'],
            'avg_dimensions.width' => ['required', 'numeric'],
            'avg_dimensions.height' => ['required', 'numeric'],
            'avg_dimensions.length' => ['required', 'numeric'],
            'avg_dimensions.unit' => ['required', 'string'],
            'total_stock' => ['nullable', 'integer'],
            'min_stock' => ['nullable', 'integer'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'meta_keywords' => ['nullable', 'array'],
            'active' => ['required', 'boolean'],
            'is_featured' => ['required', 'boolean'],
            'is_new' => ['required', 'boolean'],
            'has_variants' => ['required', 'boolean'],
            'available_at' => ['required', 'date'],
        ];
    }

    /**
     * Merge route id into validation data as 'id'
     */
    public function validationData()
    {
        $routeParameters = $this->route() ? $this->route()->parameters() : [];

        return array_merge($this->all(), $routeParameters);
    }

    protected function failedValidation(Validator $validator)
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

    public function authorize()
    {
        return true;
    }
}
