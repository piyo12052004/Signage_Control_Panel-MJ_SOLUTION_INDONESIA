<?php

namespace App\Http\Requests\Content;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'type' => [
                'sometimes',
                'required',
                'string',
                Rule::in(['image', 'video', 'html', 'url']),
            ],
            'payload' => [
                'nullable',
                'array',
                'required_if:type,html',
            ],
            'url' => [
                'nullable',
                'url',
                'required_if:type,image,video,url',
            ],
        ];
    }
}
