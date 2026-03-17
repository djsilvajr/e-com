<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Exceptions\InvalidParametersException;

class GetProductByIdRequest extends FormRequest
{
    public function rules()
    {
        return [
            'id' => ['required', 'integer', 'min:1'],
        ];
    }

    /**
     * Inclui parâmetros da rota (ex: {id}) nos dados que serão validados.
     * Deve ser public para ser compatível com FormRequest::validationData().
     */
    public function validationData()
    {
        $routeParameters = $this->route() ? $this->route()->parameters() : [];

        return array_merge($this->all(), $routeParameters);
    }

    /**
     * Sobrescreve a falha de validação para devolver JSON 422.
     * Assinatura correta: Illuminate\Contracts\Validation\Validator
     */
    protected function failedValidation(Validator $validator)
    {
         throw new InvalidParametersException('Invalid parameters.', [$validator->errors()->first()]);
    }

    public function authorize()
    {
        return true;
    }
}
