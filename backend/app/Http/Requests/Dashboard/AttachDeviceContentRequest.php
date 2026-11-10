<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class AttachDeviceContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content_id' => [
                'required',
                'uuid',
                'exists:contents,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'content_id.required' => 'Content wajib dipilih.',
            'content_id.uuid' => 'Format ID content tidak valid.',
            'content_id.exists' => 'Content tidak ditemukan.',
        ];
    }
}
