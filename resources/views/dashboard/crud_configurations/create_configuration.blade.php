@extends('layout.dashboard_layout')

@section('content')
    <div class="h-full bg-gray-200 p-8">
        <div class="m-auto bg-white p-4 max-w-5xl flex justify-center gap-8 rounded-lg shadow-xl py-8 mt-12">
            <div class="w-full px-6">
                <h4 class="text-xl font-bold capitalize text-gray-800">Crear Nueva Configuración</h4>

                <!-- Error Messages -->
                @if ($errors->any())
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if ($errors->has('error'))
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
                        {{ $errors->first('error') }}
                    </div>
                @endif

                <!-- Configuration Form -->
                <form action="{{ route('configuration.store') }}" method="POST" id="configForm">
                    @csrf

                    <!-- Configuration Name -->
                    <div class="flex flex-col mb-6">
                        <label for="name" class="text-gray-700 font-medium mb-1">Nombre de la configuración*</label>
                        <input type="text" name="name" id="name"
                            class="flex-1 py-2 px-3 border border-gray-300 mt-1 rounded focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                            placeholder="Ej: Configuración de usuarios" value="{{ old('name') }}" required>
                        <p class="text-sm text-gray-500 mt-1">Este nombre identificará tu configuración en el sistema.</p>
                        @error('name')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Configuration Fields -->
                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-4">
                            <label class="text-gray-700 font-medium">Campos de la configuración</label>
                            <button type="button" id="addField"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded text-sm flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                Agregar Campo
                            </button>
                        </div>
                        <p class="text-sm text-gray-500 mb-4">Define los campos que tendrá tu configuración. Cada campo debe
                            tener un nombre único.</p>

                        <div id="fieldsContainer">
                            @if (old('fields'))
                                @foreach (old('fields') as $index => $field)
                                    @include('dashboard.crud_configurations.field_template', [
                                        'index' => $index,
                                        'field' => $field,
                                    ])
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-2 mt-6">
                        <a href="{{ route('configuration.index') }}"
                            class="bg-gray-300 hover:bg-gray-400 rounded-lg px-6 py-2 text-gray-700 hover:shadow-xl transition duration-150">Cancelar</a>
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 rounded-lg px-6 py-2 text-white hover:shadow-xl transition duration-150">Crear
                            Configuración</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('custom-script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fieldsContainer = document.getElementById('fieldsContainer');
            const addFieldButton = document.getElementById('addField');
            let fieldCount = {{ old('fields') ? count(old('fields')) : 0 }};

            // Utility Functions
            function getNextLetter(letter) {
                if (!letter) return 'A';

                let lastChar = letter.toUpperCase();
                let result = '';
                let carry = true;

                // Convertir a array de caracteres y procesar de derecha a izquierda
                const chars = lastChar.split('').reverse();

                for (let i = 0; i < chars.length; i++) {
                    let charCode = chars[i].charCodeAt(0);

                    if (carry) {
                        charCode++;
                        if (charCode > 90) { // 'Z'
                            charCode = 65; // 'A'
                            carry = true;
                        } else {
                            carry = false;
                        }
                    }

                    result = String.fromCharCode(charCode) + result;
                }

                // Si todavía hay carry, agregar 'A' al principio
                if (carry) {
                    result = 'A' + result;
                }

                return result;
            }

            function parseExcelCell(cell) {
                const matches = cell.match(/^([A-Z]+)(\d+)$/);
                return matches ? {
                    column: matches[1],
                    row: matches[2]
                } : null;
            }

            function getNextExcelCell() {
                const excelCells = document.querySelectorAll('.excel-cell');
                const usedCells = new Set();

                // Recopilar todas las celdas válidas
                excelCells.forEach(input => {
                    const cellValue = input.value.trim().toUpperCase();
                    if (isValidExcelCell(cellValue)) {
                        usedCells.add(cellValue);
                    }
                });

                // Generar celdas en orden A1, B1, C1... Z1, A2, B2, etc.
                const columns = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.split('');

                for (let row = 1; row <= 100; row++) {
                    for (let col = 0; col < columns.length; col++) {
                        const cell = columns[col] + row;
                        if (!usedCells.has(cell)) {
                            return cell;
                        }
                    }
                }

                // Fallback si todas las celdas están ocupadas (poco probable)
                return 'A1';
            }

            function isValidExcelCell(cell) {
                return /^[A-Z]{1,3}[0-9]{1,4}$/.test(cell);
            }

            // Add New Field
            addFieldButton.addEventListener('click', function() {
                const nextCell = getNextExcelCell();
                const fieldGroup = document.createElement('div');
                fieldGroup.className = 'field-group border border-gray-200 rounded-lg p-4 mb-4 relative';
                fieldGroup.draggable = true;
                fieldGroup.innerHTML = `
                    <button type="button" class="absolute top-2 right-2 text-red-500 hover:text-red-700 remove-field">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-3">
                        <div class="flex flex-col">
                            <label class="text-gray-700">Nombre del campo*</label>
                            <input type="text" name="fields[${fieldCount}][name]"
                                   class="flex-1 py-1 border-gray-300 mt-1 rounded focus:border-gray-300 focus:outline-none focus:ring-0 field-name"
                                   placeholder="Ej: email, username, etc." required>
                        </div>
                        <div class="flex flex-col">
                            <label class="text-gray-700">Celda de Excel*</label>
                            <input type="text" name="fields[${fieldCount}][excel_cell]"
                                   class="flex-1 py-1 border-gray-300 mt-1 rounded focus:border-gray-300 focus:outline-none focus:ring-0 excel-cell uppercase"
                                   placeholder="Ej: A1, B3, C9, etc."
                                   value="${nextCell}"
                                   pattern="[A-Z]{1,3}[0-9]{1,4}" title="Formato válido: Letra(s) seguida(s) de número(s). Ej: A1, AB23, ABC123" required>
                            <p class="text-xs text-gray-500 mt-1">Formato: Letra(s) + Número(s). Ej: A1, B23, AA45</p>
                        </div>
                        <div class="flex flex-col">
                            <label class="text-gray-700">Campo Doctor Requerido</label>
                            <select class="doctor-field-select flex-1 py-1 border-gray-300 mt-1 rounded focus:border-gray-300 focus:outline-none focus:ring-0">
                                <option value="">Ninguno</option>
                                @foreach ($doctorFieldsRequired as $dfield)
                                    <option value="{{ $dfield }}">{{ ucfirst($dfield) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="doctor-hidden-inputs"></div>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="flex items-center">
                            <input type="checkbox" name="fields[${fieldCount}][required]" checked
                                   value="1" class="required-checkbox rounded border-gray-300 text-blue-600 focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            <label class="ml-2 text-gray-700">Requerido</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="fields[${fieldCount}][unique]"
                                   value="1" class="unique-checkbox rounded border-gray-300 text-blue-600 focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" disabled>
                            <label class="ml-2 text-gray-700">Único</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="fields[${fieldCount}][filterable]"
                                   value="1" class="rounded border-gray-300 text-blue-600 focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            <label class="ml-2 text-gray-700">Filtrable</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="fields[${fieldCount}][searchable]" checked
                                   value="1" class="rounded border-gray-300 text-blue-600 focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            <label class="ml-2 text-gray-700">Buscable</label>
                        </div>
                    </div>
                `;

                fieldsContainer.prepend(fieldGroup);
                fieldCount++;

                // Event Listeners for New Field
                const removeButton = fieldGroup.querySelector('.remove-field');
                removeButton.addEventListener('click', () => fieldGroup.remove());

                const requiredCheckbox = fieldGroup.querySelector('.required-checkbox');
                const uniqueCheckbox = fieldGroup.querySelector('.unique-checkbox');
                requiredCheckbox.addEventListener('change', function() {
                    uniqueCheckbox.disabled = !this.checked;
                    if (!this.checked) uniqueCheckbox.checked = false;
                });

                const excelCellInput = fieldGroup.querySelector('.excel-cell');
                excelCellInput.addEventListener('input', () => excelCellInput.value = excelCellInput.value
                    .toUpperCase());
                excelCellInput.addEventListener('blur', function() {
                    this.setCustomValidity(
                        this.value && !isValidExcelCell(this.value) ?
                        'Por favor ingresa un formato válido de celda de Excel (Ej: A1, B23, AA45)' :
                        ''
                    );
                });

                const doctorSelect = fieldGroup.querySelector('.doctor-field-select');
                const hiddenInputs = fieldGroup.querySelector('.doctor-hidden-inputs');
                doctorSelect.addEventListener('change', function() {
                    hiddenInputs.innerHTML = '';
                    const value = this.value;
                    if (value) {
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = `fields[${fieldCount - 1}][${value}]`;
                        hiddenInput.value = 'true';
                        hiddenInputs.appendChild(hiddenInput);
                    }
                });

                // Drag events
                fieldGroup.addEventListener('dragstart', () => fieldGroup.classList.add('dragging'));
                fieldGroup.addEventListener('dragend', () => fieldGroup.classList.remove('dragging'));
            });

            // Initialize Existing Fields
            document.querySelectorAll('.field-group').forEach(fieldGroup => {
                const removeButton = fieldGroup.querySelector('.remove-field');
                removeButton.addEventListener('click', () => fieldGroup.remove());

                const requiredCheckbox = fieldGroup.querySelector('.required-checkbox');
                const uniqueCheckbox = fieldGroup.querySelector('.unique-checkbox');
                if (!requiredCheckbox.checked) uniqueCheckbox.disabled = true;
                requiredCheckbox.addEventListener('change', function() {
                    uniqueCheckbox.disabled = !this.checked;
                    if (!this.checked) uniqueCheckbox.checked = false;
                });

                const excelCellInput = fieldGroup.querySelector('.excel-cell');
                excelCellInput.addEventListener('input', () => excelCellInput.value = excelCellInput.value
                    .toUpperCase());
                excelCellInput.addEventListener('blur', function() {
                    this.setCustomValidity(
                        this.value && !isValidExcelCell(this.value) ?
                        'Por favor ingresa un formato válido de celda de Excel (Ej: A1, B23, AA45)' :
                        ''
                    );
                });

                // Assume field_template has .doctor-field-select and .doctor-hidden-inputs
                const doctorSelect = fieldGroup.querySelector('.doctor-field-select');
                const hiddenInputs = fieldGroup.querySelector('.doctor-hidden-inputs');
                if (doctorSelect && hiddenInputs) {
                    doctorSelect.addEventListener('change', function() {
                        hiddenInputs.innerHTML = '';
                        const value = this.value;
                        if (value) {
                            const hiddenInput = document.createElement('input');
                            hiddenInput.type = 'hidden';
                            hiddenInput.name =
                                `fields[${fieldGroup.dataset.index}][${value}]`; // Assume data-index from template
                            hiddenInput.value = 'true';
                            hiddenInputs.appendChild(hiddenInput);
                        }
                    });
                    // Trigger initial if old value
                    doctorSelect.dispatchEvent(new Event('change'));
                }

                // Drag events
                fieldGroup.draggable = true;
                fieldGroup.addEventListener('dragstart', () => fieldGroup.classList.add('dragging'));
                fieldGroup.addEventListener('dragend', () => fieldGroup.classList.remove('dragging'));
            });

            // Sortable Drag and Drop
            fieldsContainer.addEventListener('dragover', e => {
                e.preventDefault();
                const afterElement = getDragAfterElement(fieldsContainer, e.clientY);
                const draggable = document.querySelector('.dragging');
                if (afterElement == null) {
                    fieldsContainer.appendChild(draggable);
                } else {
                    fieldsContainer.insertBefore(draggable, afterElement);
                }
            });

            function getDragAfterElement(container, y) {
                const draggableElements = [...container.querySelectorAll('.field-group:not(.dragging)')];
                return draggableElements.reduce((closest, child) => {
                    const box = child.getBoundingClientRect();
                    const offset = y - box.top - box.height / 2;
                    if (offset < 0 && offset > closest.offset) {
                        return {
                            offset: offset,
                            element: child
                        };
                    } else {
                        return closest;
                    }
                }, {
                    offset: Number.NEGATIVE_INFINITY
                }).element;
            }

            // Form Submission Validation
            document.querySelector('form').addEventListener('submit', function(event) {
                const excelCells = new Set();
                let hasDuplicates = false;

                // Validate unique Excel cells
                document.querySelectorAll('.excel-cell').forEach(input => {
                    const cellValue = input.value.trim();
                    if (cellValue) {
                        if (excelCells.has(cellValue)) {
                            hasDuplicates = true;
                            input.setCustomValidity(
                                'Esta celda de Excel ya está siendo utilizada por otro campo');
                        } else {
                            excelCells.add(cellValue);
                            input.setCustomValidity('');
                        }
                    }
                });

                if (hasDuplicates) {
                    event.preventDefault();
                    alert(
                        'Por favor, corrige las celdas de Excel duplicadas. Cada campo debe tener una celda única.'
                    );
                }
            });
        });
    </script>
@endsection
