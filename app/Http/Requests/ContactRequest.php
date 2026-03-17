<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'    => ['required', 'string', 'min:2', 'max:100'],
            'email'   => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'min:3', 'max:255'],
            'message' => ['required', 'string', 'min:10', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.min'       => 'Le nom doit contenir au moins :min caractères.',
            'subject.min'    => 'Le sujet doit contenir au moins :min caractères.',
            'message.min'    => 'Le message doit contenir au moins :min caractères.',
            'message.max'    => 'Le message ne peut pas dépasser :max caractères.',
        ];
    }
}
