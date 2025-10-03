<?php

namespace App\Http\Requests;

use App\Rules\Cron\ValidCronExpression;
use Illuminate\Foundation\Http\FormRequest;

class CreateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'origin_id' => 'required|exists:origins,id',
            'destination_id' => 'required|exists:destinations,id',
            'schedule_expression' => ['required', 'string', 'max:100',  new ValidCronExpression],
        ];
    }

    public function messages(): array
    {
        return [
            'origin_id.required' => 'Please select an origin.',
            'origin_id.exists' => 'The selected origin does not exist.',
            'destination_id.required' => 'Please select a destination.',
            'destination_id.exists' => 'The selected destination does not exist.',
            'schedule_expression.required' => 'Please provide a schedule expression.',
            'schedule_expression.max' => 'Schedule expression may not exceed 100 characters.'
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'schedule_expression' => trim($this->schedule_expression ?? ''),
        ]);
    }
}
