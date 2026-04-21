<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:50'],
            'last_name'  => ['required', 'string', 'max:50'],
            'email'      => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password'   => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers(),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'Le prénom est obligatoire.',
            'first_name.max'      => 'Le prénom ne peut pas dépasser 50 caractères.',
            'last_name.required'  => 'Le nom est obligatoire.',
            'last_name.max'       => 'Le nom ne peut pas dépasser 50 caractères.',
            'email.required'      => 'L\'adresse e-mail est obligatoire.',
            'email.email'         => 'L\'adresse e-mail n\'est pas valide.',
            'email.unique'        => 'Cette adresse e-mail est déjà utilisée.',
            'password.required'   => 'Le mot de passe est obligatoire.',
            'password.confirmed'  => 'Les mots de passe ne correspondent pas.',
            'password.min'        => 'Le mot de passe doit contenir au moins 8 caractères.',
            'terms.accepted'      => 'Vous devez accepter les conditions d\'utilisation.',
        ];
    }
}
