<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Exceptions\InvalidParametersException;

class CreateProductRequest extends FormRequest
{
    public function rules()
    {
        return [
            'product_type_id' => ['required', 'integer', 'min:1'],
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:255', 'unique:products,sku'],
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
     * Merge route parameters into validation data.
     */
    public function validationData()
    {
        $routeParameters = $this->route() ? $this->route()->parameters() : [];

        return array_merge($this->all(), $routeParameters);
    }

    /**
     * Override failed validation to throw domain-specific exception.
     */
    protected function failedValidation(Validator $validator)
    {
        // todas as mensagens em formato: [ 'campo' => ['msg1', 'msg2'], ... ]
        $allMessages = $validator->errors()->messages();

        // formatar para: [ 'campo' => 'primeira mensagem' , ... ]
        $formatted = [];
        foreach ($allMessages as $field => $messages) {
            $formatted[$field] = isset($messages[0]) ? $messages[0] : implode(' ', $messages);
        }

        // opcional: pegar o primeiro campo/erro para compatibilidade com implementações que esperam uma mensagem simples
        $firstField = count($formatted) ? array_key_first($formatted) : null;
        $firstMessage = $firstField ? $formatted[$firstField] : 'Invalid parameters.';

        // lançar exceção com payload estruturado contendo o campo e as mensagens
        // ajuste o construtor de InvalidParametersException conforme sua implementação
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
