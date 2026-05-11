<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class ChangeAdminProductTypeStatusRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id'     => ['required', 'integer', 'min:1'],
            'status' => ['required', 'string', 'in:TRUE,FALSE'],
        ];
    }

    public function messages(): array
    {
        return [
            'id.required'     => 'O tipo de produto é obrigatório.',
            'id.integer'      => 'O tipo de produto informado é inválido.',
            'id.min'          => 'O tipo de produto informado é inválido.',
            'status.required' => 'O status é obrigatório.',
            'status.in'       => 'O status informado é inválido.',
        ];
    }

    public function validationData(): array
    {
        $routeParameters = $this->route() ? $this->route()->parameters() : [];
        $merged = array_merge($this->all(), $routeParameters);

        if (isset($merged['status']) && is_string($merged['status'])) {
            $merged['status'] = strtoupper($merged['status']);
        }

        return $merged;
    }

    protected function failedValidation(Validator $validator): void
    {
        throw (new ValidationException($validator))
            ->redirectTo(route('admin.types.index'));
    }

    public function authorize(): bool
    {
        return true;
    }
}
