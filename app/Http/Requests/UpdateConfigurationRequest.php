<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateConfigurationRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('configurations', 'name')->ignore($this->route('config')),
            ],
            'fields' => 'required|array|min:1',
            'fields.*.name' => 'required|string|distinct|max:100',
            'fields.*.excel_cell' => [
                'required',
                'string',
                'regex:/^[A-Z]{1,3}[0-9]{1,4}$/',
                'distinct',
            ],
            'fields.*.required' => 'sometimes|boolean',
            'fields.*.unique' => 'sometimes|boolean',
            'fields.*.filterable' => 'sometimes|boolean',
            'fields.*.searchable' => 'sometimes|boolean',
            'fields.*.start_date' => 'sometimes|boolean',
            'fields.*.end_date' => 'sometimes|boolean',
            'fields.*.ci' => 'sometimes|boolean',
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre de la configuración es obligatorio.',
            'name.unique' => 'Ya existe una configuración con este nombre.',
            'fields.required' => 'Debe agregar al menos un campo a la configuración.',
            'fields.min' => 'Debe agregar al menos un campo a la configuración.',
            'fields.*.name.required' => 'El nombre del campo es obligatorio.',
            'fields.*.name.distinct' => 'Los nombres de los campos no pueden repetirse.',
            'fields.*.excel_cell.required' => 'La celda de Excel es obligatoria.',
            'fields.*.excel_cell.regex' => 'El formato de la celda de Excel no es válido. Use formato como A1, B23, AA45.',
            'fields.*.excel_cell.distinct' => 'Las celdas de Excel no pueden repetirse.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        $preparedFields = [];

        if ($this->has('fields')) {
            foreach ($this->fields as $index => $field) {
                $preparedFields[$index] = [
                    'name' => $field['name'] ?? null,
                    'excel_cell' => isset($field['excel_cell']) ? strtoupper($field['excel_cell']) : null,
                    'required' => isset($field['required']) && $field['required'] == '1',
                    'unique' => isset($field['unique']) && $field['unique'] == '1',
                    'ci' => isset($field['ci']),
                    'start_date' => isset($field['start_date']),
                    'end_date' => isset($field['end_date']),
                    'filterable' => isset($field['filterable']) && $field['filterable'] == '1',
                    'searchable' => isset($field['searchable']) && $field['searchable'] == '1',
                ];
            }
        }

        $this->merge([
            'fields' => $preparedFields,
        ]);

        // Custom validation for date fields
        $this->validateDateFields();
    }

    /**
     * Custom validation for date fields.
     */
    protected function validateDateFields()
    {
        $fields = $this->input('fields', []);

        // Count fields with start_date and end_date set to true
        $startDateCount = 0;
        $endDateCount = 0;
        $invalidFields = [];

        foreach ($fields as $index => $field) {
            $hasStartDate = isset($field['start_date']) && $field['start_date'];
            $hasEndDate = isset($field['end_date']) && $field['end_date'];

            // Check if both start and end dates are set for the same field
            if ($hasStartDate && $hasEndDate) {
                $invalidFields[] = "fields.{$index}.start_date";
                $invalidFields[] = "fields.{$index}.end_date";
                $this->validator->errors()->add(
                    "fields.{$index}.start_date",
                    'Un campo no puede ser both fecha de inicio y fecha de culminación.'
                );
            }

            // Count start and end dates
            if ($hasStartDate) {
                $startDateCount++;
            }
            if ($hasEndDate) {
                $endDateCount++;
            }
        }

        // Validate that there is at most one start date
        if ($startDateCount > 1) {
            foreach ($fields as $index => $field) {
                if (isset($field['start_date']) && $field['start_date']) {
                    $invalidFields[] = "fields.{$index}.start_date";
                    $this->validator->errors()->add(
                        "fields.{$index}.start_date",
                        'Solo puede haber un campo marcado como fecha de inicio.'
                    );
                }
            }
        }

        // Validate that there is at most one end date
        if ($endDateCount > 1) {
            foreach ($fields as $index => $field) {
                if (isset($field['end_date']) && $field['end_date']) {
                    $invalidFields[] = "fields.{$index}.end_date";
                    $this->validator->errors()->add(
                        "fields.{$index}.end_date",
                        'Solo puede haber un campo marcado como fecha de culminación.'
                    );
                }
            }
        }

        // If there are invalid fields, trigger validation failure
        if (!empty($invalidFields)) {
            $this->validator->fails();
        }
    }

    /**
     * Get custom attribute names for validation errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'fields.*.name' => 'nombre del campo',
            'fields.*.excel_cell' => 'celda de Excel',
            'fields.*.start_date' => 'fecha de inicio',
            'fields.*.end_date' => 'fecha de culminación',
        ];
    }
}
