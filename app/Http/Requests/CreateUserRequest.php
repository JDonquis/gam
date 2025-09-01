<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'fullname' => ['string','required','min:3','max:100'],
            'ci' => ['string','required','min:7','max:11','unique:users,ci'],
            'phone_number' => ['string','min:10','max:20','nullable'],
            'photo' => ['nullable','image','mimes:jpeg,png,jpg','max:2048'],

        ];

    }

    public function attributes(): array
    {
        return [
            'fullname' => 'Nombres y apellidos',
            'ci' => 'Cédula',
            'phone_number' => 'Teléfono',
        ];
    }
}
