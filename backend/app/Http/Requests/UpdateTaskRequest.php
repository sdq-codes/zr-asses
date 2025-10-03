<?php

namespace App\Http\Requests;

use App\Rules\Cron\ValidCronExpression;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'origin_id' => 'sometimes|required|exists:origins,id',
            'destination_id' => 'sometimes|required|exists:destinations,id',
            'name' => 'sometimes|string|max:255',
            'schedule_expression' => ['nullable', 'string', 'max:100', new ValidCronExpression()],
            'schedule_timezone' => 'sometimes|timezone',
            'is_active' => 'boolean',
        ];
    }
}
