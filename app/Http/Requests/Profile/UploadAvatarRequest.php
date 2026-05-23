<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UploadAvatarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'avatar' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'avatar.required' => 'La imagen es requerida.',
            'avatar.image'    => 'El archivo debe ser una imagen.',
            'avatar.mimes'    => 'La imagen debe ser jpg, jpeg, png o webp.',
            'avatar.max'      => 'La imagen no puede superar 2MB.',
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