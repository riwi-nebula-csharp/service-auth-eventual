<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'          => 'sometimes|string|max:150',
            'phone'         => 'sometimes|nullable|string|max:20',
            'permissions'   => 'sometimes|array|min:1',
            'permissions.*' => 'required_with:permissions|in:tickets,access',
        ];
    }

    public function messages(): array
    {
        return [
            'name.string'       => 'El nombre debe ser texto.',
            'name.max'          => 'El nombre no puede superar 150 caracteres.',
            'phone.string'      => 'El teléfono debe ser texto.',
            'permissions.array' => 'Los permisos deben ser un array.',
            'permissions.min'   => 'Debe asignar al menos un permiso.',
            'permissions.*.in'  => 'Los permisos válidos son: tickets, access.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Error de validación.',
                'errors'  => $validator->errors(),
            ], 422)
        );
    }
}