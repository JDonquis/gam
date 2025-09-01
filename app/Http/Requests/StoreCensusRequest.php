<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCensusRequest extends FormRequest
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
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120', // 5MB máximo
            'registros' => 'required|array',
            'configuration_id' => 'required',
            'type_document_id' => 'required',
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->has('registros') && is_string($this->registros)) {
            $this->merge([
                'registros' => json_decode($this->registros, true)
            ]);
        }
    }
}
