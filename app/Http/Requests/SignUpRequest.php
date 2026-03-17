<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SignUpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name'  => ['required', 'string', 'max:100'],
            'email'      => ['required', 'email', 'unique:users,email'],
            'password'   => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'Le prénom est requis.',
            'last_name.required'  => 'Le nom est requis.',
            'email.required'      => 'L\'adresse email est requise.',
            'email.unique'        => 'Cette adresse email est déjà utilisée.',
            'password.min'        => 'Le mot de passe doit contenir au moins :min caractères.',
            'password.confirmed'  => 'Les mots de passe ne correspondent pas.',
        ];
    }
}
