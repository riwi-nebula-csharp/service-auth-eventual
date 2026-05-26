<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'  => 'sometimes|string|max:150',
            'phone' => 'sometimes|nullable|string|max:20',
        ];
    }

    public function messages(): array
    {
        return [
            'name.string'   => 'El nombre debe ser texto.',
            'name.max'      => 'El nombre no puede superar 150 caracteres.',
            'phone.string'  => 'El teléfono debe ser texto.',
            'phone.max'     => 'El teléfono no puede superar 20 caracteres.',
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