<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\DoctorStatusEnum;

class StoreDoctorRequest extends FormRequest
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
            'ci' => 'required|string|max:20|unique:doctors,ci',
            'is_foreign' => 'required',
            'data' => 'array',
            'start_date' => 'required',
            'end_date' => 'required',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */


    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        if ($this->has('data') && is_string($this->data)) {
            $this->merge([
                'data' => json_decode($this->data['data'], true)
            ]);
        }

        if (!$this->has('is_foreign')) {
            $this->merge([
                'is_foreign' => false
            ]);
        }
    }
}
