<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'ci' => ['required','string','max:10','min:8'],
            'password' => ['required','string','max:12']
        ];
    }

    public function attributes()
    {
        return [
            'ci' => 'Cédula',
            'password' => 'Contraseña',
        ];
    }
}
