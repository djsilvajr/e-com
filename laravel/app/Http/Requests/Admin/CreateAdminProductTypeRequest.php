<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class CreateAdminProductTypeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id'           => ['required', 'integer', 'min:1'],
            'name'         => ['required', 'string', 'max:255'],
            'slug'         => ['required', 'string', 'max:255'],
            'description'  => ['nullable', 'string', 'max:1000'],
            // variant_type is forced server-side from the parent in the
            // controller, so we don't require it from the client here.
            'variant_type' => ['nullable', 'string', 'in:clothing,electronics,furniture,books,simple'],
        ];
    }

    public function messages(): array
    {
        return [
            'id.required'           => 'O tipo pai é obrigatório.',
            'id.integer'            => 'O tipo pai informado é inválido.',
            'id.min'                => 'O tipo pai informado é inválido.',
            'name.required'         => 'O nome é obrigatório.',
            'name.max'              => 'O nome deve ter no máximo 255 caracteres.',
            'slug.required'         => 'O slug é obrigatório.',
            'slug.max'              => 'O slug deve ter no máximo 255 caracteres.',
            'description.max'       => 'A descrição deve ter no máximo 1000 caracteres.',
            'variant_type.in'       => 'O tipo de variante informado é inválido.',
        ];
    }

    public function validationData(): array
    {
        $routeParameters = $this->route() ? $this->route()->parameters() : [];
        return array_merge($this->all(), $routeParameters);
    }

    protected function failedValidation(Validator $validator): void
    {
        $parentId = (int) ($this->route('id') ?? 0);

        throw (new ValidationException($validator))
            ->redirectTo(route('admin.types.show', ['id' => $parentId]));
    }

    public function authorize(): bool
    {
        return true;
    }
}
