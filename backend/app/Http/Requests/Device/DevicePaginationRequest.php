<?php

namespace App\Http\Requests\Device;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DevicePaginationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'page' => [
                'sometimes',
                'integer',
                'min:1',
            ],

            'per_page' => [
                'sometimes',
                'integer',
                'min:1',
                'max:100',
            ],

            'search' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'status' => [
                'sometimes',
                'nullable',
                Rule::in(['online', 'offline']),
            ],

            'sort_by' => [
                'sometimes',
                Rule::in([
                    'name',
                    'location',
                    'status',
                    'last_seen',
                    'created_at',
                ]),
            ],

            'sort_direction' => [
                'sometimes',
                Rule::in(['asc', 'desc']),
            ],
        ];
    }
}
