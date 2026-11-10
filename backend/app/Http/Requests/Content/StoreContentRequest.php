<?php

namespace App\Http\Requests\Content;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('payload'))) {
            $payload = json_decode($this->input('payload'), true);

            if (
                json_last_error() === JSON_ERROR_NONE &&
                is_array($payload)
            ) {
                $this->merge([
                    'payload' => $payload,
                ]);
            }
        }
    }

    public function rules(): array
    {
        $fileRequired = $this->isMethod('POST')
            && ! $this->has('_method')
            && in_array($this->input('type'), ['image'], true);

        return [
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'type' => [
                'required',
                Rule::in(['image', 'html']),
            ],

            'file' => [
                $fileRequired ? 'required' : 'nullable',
                'file',
                'max:102400',
            ],

            'url' => [
                'nullable',
                'required_if:type,url',
                'url',
            ],

            'payload' => [
                'nullable',
                'array',
                'required_if:type,html',
            ],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->sometimes(
            'file',
            ['mimes:jpg,jpeg,png,webp'],
            fn ($input) => ($input->type ?? null) === 'image'
        );
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul content wajib diisi.',
            'title.max' => 'Judul maksimal 255 karakter.',

            'type.required' => 'Tipe content wajib dipilih.',
            'type.in' => 'Tipe content tidak valid.',

            'file.required' => 'File gambar atau video wajib diupload.',
            'file.file' => 'File yang diupload tidak valid.',
            'file.max' => 'Ukuran file maksimal 100 MB.',
            'file.mimes' => 'Format file tidak sesuai dengan tipe content.',

            'url.required_if' => 'URL wajib diisi untuk tipe URL.',
            'url.url' => 'Format URL tidak valid.',

            'payload.required_if' => 'Payload wajib diisi untuk tipe HTML.',
            'payload.array' => 'Payload harus berupa object JSON.',
        ];
    }
}