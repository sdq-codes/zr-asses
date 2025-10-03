<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateDestinationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'sometimes|string|max:1000',
            'image' => 'required|active_url|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The destination name is required.',
            'name.max' => 'The destination name may not exceed 255 characters.',
            'description.max' => 'The description may not exceed 1000 characters.',
            'image.active_url' => 'The image URL must be a valid URL.',
            'image.max' => 'The image URL may not exceed 500 characters.'
        ];
    }
}
