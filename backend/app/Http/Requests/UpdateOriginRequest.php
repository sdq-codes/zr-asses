<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOriginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|string|max:1000',
            'image' => 'sometimes|url|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The origin name is required.',
            'name.max' => 'The origin name must not exceed 255 characters.',
            'description.max' => 'The description must not exceed 1000 characters.',
            'image.url' => 'The image URL must be valid.',
            'image.max' => 'The image URL must not exceed 500 characters.',
        ];
    }
}
