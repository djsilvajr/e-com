<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class AdminLoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email'    => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:6', 'max:255'],
            'remember' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'    => 'E-mail é obrigatório.',
            'email.email'       => 'Informe um e-mail válido.',
            'password.required' => 'Senha é obrigatória.',
            'password.min'      => 'Senha deve ter ao menos 6 caracteres.',
        ];
    }

    public function validationData(): array
    {
        $routeParameters = $this->route() ? $this->route()->parameters() : [];
        return array_merge($this->all(), $routeParameters);
    }

    protected function failedValidation(Validator $validator): void
    {
        // Web form: redirect back with errors + old input (default Laravel flow).
        throw (new ValidationException($validator))
            ->redirectTo(route('admin.login'));
    }

    public function authorize(): bool
    {
        return true;
    }
}
