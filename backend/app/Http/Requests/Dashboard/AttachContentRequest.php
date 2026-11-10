<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class AttachContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content_ids' => ['required', 'array', 'min:1'],
            'content_ids.*' => [
                'required',
                'uuid',
                'distinct',
                'exists:contents,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'content_ids.required' => 'Pilih minimal satu content.',
            'content_ids.array' => 'Format content IDs tidak valid.',
            'content_ids.min' => 'Pilih minimal satu content.',
            'content_ids.*.exists' => 'Content yang dipilih tidak ditemukan.',
            'content_ids.*.distinct' => 'Content tidak boleh dipilih dua kali.',
        ];
    }
}
