<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule,array<mixed>|string>
     */
    public function rules(): array
    {
        $rules =  [
            'fullname' => ['string','required','min:3','max:100'],
            'ci' => ['string','required','min:7','max:11',
            Rule::unique('users')->ignore($this->user()->id),],
            'phone_number' => ['string','min:10','max:20','nullable'],
            'photo' => ['nullable','image','mimes:jpeg,png,jpg','max:2048'],

        ];

        if ($this->filled('new_password')) {
            $rules['new_password'] = [
                'required',
                'string',
                Password::min(8)
                    ->letters()
                    ->numbers()
            ];

            $rules['confirm_password'] = [
                'required',
                'same:new_password',
            ];
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'fullname' => 'Nombres y apellidos',
            'ci' => 'Cédula',
            'phone_number' => 'Teléfono',
            'new_password' => 'Nueva contraseña',
            'confirm_password' => 'Confirmar contraseña',
        ];
    }
}
