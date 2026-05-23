<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'          => 'required|string|max:150',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|string|min:8',
            'phone'         => 'nullable|string|max:20',
            'permissions'   => 'required|array|min:1',
            'permissions.*' => 'required|in:tickets,access',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'        => 'El nombre es requerido.',
            'email.required'       => 'El correo es requerido.',
            'email.email'          => 'El correo no es válido.',
            'email.unique'         => 'El correo ya está registrado.',
            'password.required'    => 'La contraseña es requerida.',
            'password.min'         => 'La contraseña debe tener mínimo 8 caracteres.',
            'permissions.required' => 'Los permisos son requeridos.',
            'permissions.array'    => 'Los permisos deben ser un array.',
            'permissions.min'      => 'Debe asignar al menos un permiso.',
            'permissions.*.in'     => 'Los permisos válidos son: tickets, access.',
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