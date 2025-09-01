<div class="field-group border border-gray-200 rounded-lg p-4 mb-4 relative" data-index="{{ $index }}"
    draggable="true">
    <button type="button" class="absolute top-2 right-2 text-red-500 hover:text-red-700 remove-field">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd"
                d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                clip-rule="evenodd" />
        </svg>
    </button>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-3">
        <div class="flex flex-col">
            <label class="text-gray-700">Nombre del campo*</label>
            <input type="text" name="fields[{{ $index }}][name]"
                class="flex-1 py-1 border-gray-300 mt-1 rounded focus:border-gray-300 focus:outline-none focus:ring-0 field-name"
                placeholder="Ej: email, username, etc."
                value="{{ old('fields.' . $index . '.name', $field['name'] ?? '') }}" required>
        </div>
        <div class="flex flex-col">
            <label class="text-gray-700">Celda de Excel*</label>
            <input type="text" name="fields[{{ $index }}][excel_cell]"
                class="flex-1 py-1 border-gray-300 mt-1 rounded focus:border-gray-300 focus:outline-none focus:ring-0 excel-cell uppercase"
                placeholder="Ej: A1, B3, C9, etc."
                value="{{ old('fields.' . $index . '.excel_cell', $field['excel_cell'] ?? '') }}"
                pattern="[A-Z]{1,3}[0-9]{1,4}"
                title="Formato válido: Letra(s) seguida(s) de número(s). Ej: A1, AB23, ABC123" required>
            <p class="text-xs text-gray-500 mt-1">Formato: Letra(s) + Número(s). Ej: A1, B23, AA45</p>
        </div>
        <div class="flex flex-col">
            <label class="text-gray-700">Campo Doctor Requerido</label>
            <select
                class="doctor-field-select flex-1 py-1 border-gray-300 mt-1 rounded focus:border-gray-300 focus:outline-none focus:ring-0"
                name="fields[{{ $index }}][doctor_field]">
                <option value="">Ninguno</option>
                @foreach ($doctorFieldsRequired as $dfield)
                    @php
                        $isSelected = false;
                        // Verificar si este campo está presente y es true en la estructura
                        if (isset($field[$dfield]) && $field[$dfield] === true) {
                            $isSelected = true;
                        }
                        // También verificar si hay un valor en old()
                        elseif (old('fields.' . $index . '.' . $dfield) === 'true') {
                            $isSelected = true;
                        }
                        // Para compatibilidad con el viejo sistema de doctor_field
                        elseif (old('fields.' . $index . '.doctor_field', $field['doctor_field'] ?? '') == $dfield) {
                            $isSelected = true;
                        }
                    @endphp
                    <option value="{{ $dfield }}" {{ $isSelected ? 'selected' : '' }}>
                        {{ ucfirst($dfield) }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="doctor-hidden-inputs">
        @foreach ($doctorFieldsRequired as $dfield)
            @php
                $shouldCreateHidden = false;

                // Verificar si este campo está presente y es true en la estructura original
                if (isset($field[$dfield]) && $field[$dfield] === true) {
                    $shouldCreateHidden = true;
                }
                // Verificar si hay un valor en old() para este campo
                elseif (old('fields.' . $index . '.' . $dfield) === 'true') {
                    $shouldCreateHidden = true;
                }
                // Para compatibilidad con el viejo sistema - si doctor_field coincide
                elseif (old('fields.' . $index . '.doctor_field', $field['doctor_field'] ?? '') == $dfield) {
                    $shouldCreateHidden = true;
                }
            @endphp
            @if ($shouldCreateHidden)
                <input type="hidden" name="fields[{{ $index }}][{{ $dfield }}]" value="true">
            @endif
        @endforeach
    </div>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="flex items-center">
            <input type="checkbox" name="fields[{{ $index }}][required]" value="1"
                class="required-checkbox rounded border-gray-300 text-blue-600 focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                {{ old('fields.' . $index . '.required', $field['required'] ?? '') ? 'checked' : '' }}>
            <label class="ml-2 text-gray-700">Requerido</label>
        </div>
        <div class="flex items-center">
            <input type="checkbox" name="fields[{{ $index }}][unique]" value="1"
                class="unique-checkbox rounded border-gray-300 text-blue-600 focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                {{ old('fields.' . $index . '.required', $field['required'] ?? '') ? '' : 'disabled' }}
                {{ old('fields.' . $index . '.unique', $field['unique'] ?? '') ? 'checked' : '' }}>
            <label class="ml-2 text-gray-700">Único</label>
        </div>
        <div class="flex items-center">
            <input type="checkbox" name="fields[{{ $index }}][filterable]" value="1"
                class="rounded border-gray-300 text-blue-600 focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                {{ old('fields.' . $index . '.filterable', $field['filterable'] ?? '') ? 'checked' : '' }}>
            <label class="ml-2 text-gray-700">Filtrable</label>
        </div>
        <div class="flex items-center">
            <input type="checkbox" name="fields[{{ $index }}][searchable]" value="1"
                class="rounded border-gray-300 text-blue-600 focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                {{ old('fields.' . $index . '.searchable', $field['searchable'] ?? '') ? 'checked' : 'checked' }}>
            <label class="ml-2 text-gray-700">Buscable</label>
        </div>
    </div>
</div>
