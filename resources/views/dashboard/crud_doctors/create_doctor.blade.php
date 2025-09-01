@extends('layout.dashboard_layout')
@section('content')

    <div class="h-full bg-gray-200 p-8">
        <div class="m-auto bg-white p-4 max-w-6xl flex justify-center gap-8 rounded-lg shadow-xl py-8 mt-12">
            <div class="w-full">
                <h4 class="text-xl capitalize">Registrar Nuevo Médico</h4>
                <div class="mt-6">
                    @if ($errors->any())
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{ route('doctors.store') }}" method="POST" id="doctorForm">
                        @csrf

                        <!-- Inputs ocultos para start_date y end_date -->
                        <input type="hidden" name="start_date" id="hidden_start_date" value="{{ old('start_date') }}">
                        <input type="hidden" name="end_date" id="hidden_end_date" value="{{ old('end_date') }}">

                        <!-- Sección 0: Registro Automático (Opcional) -->
                        <div class="bg-blue-50 p-6 rounded-lg mb-6 border border-blue-200">
                            <h5 class="text-lg font-semibold text-blue-800 mb-4">
                                <span class="inline-flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                    Registro Automático (Opcional)
                                </span>
                            </h5>

                            <div class="flex flex-col">
                                <label for="auto_register" class="text-gray-700 mb-2">
                                    Pegar datos en formato CSV:
                                </label>
                                <textarea name="auto_register" id="auto_register" rows="4"
                                    class="w-full py-2 px-3 border-gray-300 rounded focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                    placeholder="Ej: 1,FALCÓN,17.518.270,NO APLICA,VENEZOLANOS,VENEZUELA,VARGAS RIVERO ELISMAR COROMOTO ,F,19/02/1984,41,MEDICO INTEGRAL COMUNITARIO,UNEFM,0426-3237673,ELISMARVARGAS1984@GMAIL.COM,01/01/2024,31/12/2025,2,HOSPITAL UNIVERSITARIO DR ALFREDO VAN GRIEKEN,EPIDEMIOLOGIA,IAEAG,,,,1,,,1,,,,2,">{{ old('auto_register') }}</textarea>
                                <p class="text-sm text-gray-500 mt-2">
                                    Pegue los datos en formato CSV separados por comas. El sistema intentará mapearlos
                                    automáticamente a los campos correspondientes.
                                </p>

                                <div class="mt-4 flex gap-2">
                                    <button type="button" onclick="parseAutoRegister()"
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
                                        Procesar Datos
                                    </button>
                                    <button type="button" onclick="clearAutoRegister()"
                                        class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded text-sm">
                                        Limpiar
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Sección 1: Selección de Configuración -->
                        <div class="bg-gray-50 p-6 rounded-lg mb-6">
                            <h5 class="text-lg font-semibold text-gray-800 mb-4">1. Seleccionar Configuración</h5>

                            <div class="flex flex-col">
                                <label for="configuration_id" class="text-gray-700">Configuración*</label>
                                <select name="configuration_id" id="configuration_id"
                                    class="flex-1 py-2 border-gray-300 mt-1 rounded focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                                    required onchange="loadConfigurationFields(this.value)">
                                    <option value="">Seleccione una configuración</option>
                                    @foreach ($configurations as $configuration)
                                        <option value="{{ $configuration->id }}"
                                            {{ old('configuration_id') == $configuration->id ? 'selected' : '' }}
                                            data-structure='@json($configuration->structure)'>
                                            {{ $configuration->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-sm text-gray-500 mt-1">
                                    Seleccione la configuración que define los campos a completar
                                </p>
                            </div>
                        </div>

                        <!-- Sección 2: Cédula y Nacionalidad -->
                        <div class="bg-gray-50 p-6 rounded-lg mb-6" id="basicInfoSection" style="display: none;">
                            <h5 class="text-lg font-semibold text-gray-800 mb-4">2. Identificación y Nacionalidad</h5>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="flex flex-col">
                                    <label for="ci" class="text-gray-700">Cédula*</label>
                                    <input type="text" name="ci" id="ci"
                                        class="flex-1 py-2 border-gray-300 mt-1 rounded focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                                        placeholder="Ej: 12345678" value="{{ old('ci') }}" required>
                                </div>

                                <div class="flex flex-col justify-center">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="is_foreign" id="is_foreign"
                                            class="rounded border-gray-300 text-primary focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                            value="1" {{ old('is_foreign') ? 'checked' : '' }}>
                                        <span class="ml-2 text-gray-700 font-medium">Es extranjero</span>
                                    </label>
                                    <p class="text-sm text-gray-500 mt-1">Marcar si el médico es extranjero</p>
                                </div>
                            </div>
                        </div>

                        <!-- Sección 3: Campos de la Configuración -->
                        <div class="bg-gray-50 p-6 rounded-lg mb-6" id="configurationFieldsSection" style="display: none;">
                            <h5 class="text-lg font-semibold text-gray-800 mb-4">3. Información del Médico</h5>

                            <div id="configurationFieldsContainer" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Los campos se cargarán dinámicamente aquí -->
                            </div>
                        </div>



                        <!-- Botones de acción -->
                        <div class="flex gap-2 mt-8">
                            <a href="{{ route('doctors.index') }}" type="button"
                                class="bg-secondary bg-opacity-20 hover:bg-opacity-40 rounded-lg px-6 py-2 text-secondary hover:shadow-xl transition duration-150">
                                Cancelar
                            </a>
                            <button type="submit" id="submitButton" disabled
                                class="bg-primary hover:bg-primary-dark rounded-lg px-6 py-2 text-white hover:shadow-xl transition duration-150 opacity-50 cursor-not-allowed">
                                Registrar Médico
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('custom-script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Almacenar los valores antiguos del formulario
            const oldData = @json(old('data', []));

            // Función para cargar campos de la configuración
            window.loadConfigurationFields = function(configId) {
                const basicInfoSection = document.getElementById('basicInfoSection');
                const configSection = document.getElementById('configurationFieldsSection');
                const submitButton = document.getElementById('submitButton');

                const selectedOption = document.querySelector(`#configuration_id option[value="${configId}"]`);

                if (selectedOption && selectedOption.dataset.structure) {
                    // Mostrar secciones básicas
                    basicInfoSection.style.display = 'block';
                    configSection.style.display = 'block';
                    submitButton.disabled = false;
                    submitButton.classList.remove('opacity-50', 'cursor-not-allowed');

                    try {
                        // Parsear la estructura desde el data attribute
                        const fields = JSON.parse(selectedOption.dataset.structure);
                        const container = document.getElementById('configurationFieldsContainer');
                        container.innerHTML = '';

                        // Variables para almacenar índices de fechas
                        let startDateIndex = -1;
                        let endDateIndex = -1;

                        fields.forEach((field, index) => {
                            // Crear un ID seguro para el campo
                            const fieldId = 'data_' + field.name.replace(/[^a-zA-Z0-9]/g, '_');

                            // Obtener el valor antiguo si existe
                            const oldValue = oldData[field.name] || '';

                            // Guardar índices de fechas
                            if (field.start_date) startDateIndex = index;
                            if (field.end_date) endDateIndex = index;

                            const fieldHtml = `
                            <div class="flex flex-col">
                                <label for="${fieldId}" class="text-gray-700">
                                    ${field.name}${field.required ? '*' : ''}
                                </label>
                                <input type="text"
                                       name="data[${field.name}]"
                                       id="${fieldId}"
                                       class="flex-1 py-2 border-gray-300 mt-1 rounded focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                                       placeholder="Ingrese ${field.name}"
                                       value="${oldValue.replace(/"/g, '&quot;')}"
                                       ${field.required ? 'required' : ''}
                                       onchange="updateHiddenDates(${startDateIndex}, ${endDateIndex})">
                                ${field.ci ? '<p class="text-sm text-green-600 mt-1">Este campo se usará como cédula</p>' : ''}
                                ${field.start_date ? '<p class="text-sm text-blue-600 mt-1">Fecha de inicio del curso</p>' : ''}
                                ${field.end_date ? '<p class="text-sm text-blue-600 mt-1">Fecha de fin del curso</p>' : ''}
                            </div>
                        `;
                            container.insertAdjacentHTML('beforeend', fieldHtml);
                        });

                        // Actualizar fechas ocultas con valores iniciales
                        updateHiddenDates(startDateIndex, endDateIndex);

                    } catch (error) {
                        console.error('Error parsing configuration structure:', error);
                        container.innerHTML =
                            '<div class="col-span-2 text-center text-red-600">Error al cargar los campos de la configuración</div>';
                    }
                } else {
                    // Ocultar secciones si no hay configuración seleccionada
                    basicInfoSection.style.display = 'none';
                    configSection.style.display = 'none';
                    submitButton.disabled = true;
                    submitButton.classList.add('opacity-50', 'cursor-not-allowed');
                }
            };

            // Función para actualizar los inputs ocultos de fechas
            window.updateHiddenDates = function(startDateIndex, endDateIndex) {
                if (startDateIndex !== -1) {
                    const startDateField = document.querySelector(
                        `input[name^="data"]:nth-of-type(${startDateIndex + 1})`);
                    if (startDateField && startDateField.value) {
                        document.getElementById('hidden_start_date').value = startDateField.value;
                    }
                }

                if (endDateIndex !== -1) {
                    const endDateField = document.querySelector(
                        `input[name^="data"]:nth-of-type(${endDateIndex + 1})`);
                    if (endDateField && endDateField.value) {
                        document.getElementById('hidden_end_date').value = endDateField.value;
                    }
                }
            };

            // Función para procesar el registro automático
            window.parseAutoRegister = function() {
                const csvData = document.getElementById('auto_register').value.trim();

                if (!csvData) {
                    alert('Por favor, pegue los datos en formato CSV');
                    return;
                }

                // Dividir por comas y limpiar los valores
                const values = csvData.split(',').map(value => {
                    const trimmed = value.trim();
                    return (trimmed === '' || trimmed === 'NO APLICA' || trimmed === 'N/A') ? null :
                        trimmed;
                });

                // Buscar automáticamente la cédula (generalmente en posición 2 o 3)
                let ciValue = values[2] || values[1] || null;
                if (ciValue) {
                    document.getElementById('ci').value = ciValue;
                }

                // Verificar si es extranjero basado en nacionalidad
                const nacionalidad = values[4] || '';
                if (nacionalidad && nacionalidad.toLowerCase().includes('extranj') ||
                    (values[5] && values[5].toLowerCase() !== 'venezuela')) {
                    document.getElementById('is_foreign').checked = true;
                }

                // Si hay una configuración seleccionada, intentar mapear los campos
                const configId = document.getElementById('configuration_id').value;
                if (configId) {
                    const selectedOption = document.querySelector(
                        `#configuration_id option[value="${configId}"]`);
                    if (selectedOption && selectedOption.dataset.structure) {
                        try {
                            const fields = JSON.parse(selectedOption.dataset.structure);
                            let startDateIndex = -1;
                            let endDateIndex = -1;

                            fields.forEach((field, index) => {
                                if (index < values.length && values[index] !== null) {
                                    const fieldId = 'data_' + field.name.replace(/[^a-zA-Z0-9]/g, '_');
                                    const fieldElement = document.getElementById(fieldId);

                                    if (fieldElement) {
                                        fieldElement.value = values[index];

                                        // Guardar índices de fechas
                                        if (field.start_date) startDateIndex = index;
                                        if (field.end_date) endDateIndex = index;
                                    }
                                }
                            });

                            // Actualizar inputs ocultos de fechas
                            if (startDateIndex !== -1 && values[startDateIndex]) {
                                document.getElementById('hidden_start_date').value = values[startDateIndex];
                            }
                            if (endDateIndex !== -1 && values[endDateIndex]) {
                                document.getElementById('hidden_end_date').value = values[endDateIndex];
                            }

                            alert(
                                'Datos procesados correctamente. Revise y complete los campos si es necesario.'
                                );
                        } catch (error) {
                            console.error('Error processing data:', error);
                            alert('Error al procesar los datos. Por favor, complete los campos manualmente.');
                        }
                    }
                } else {
                    alert(
                        'Datos CSV procesados. Por favor, seleccione una configuración para completar automáticamente los campos.'
                        );
                }
            };

            // Función para limpiar el área de registro automático
            window.clearAutoRegister = function() {
                document.getElementById('auto_register').value = '';
            };

            // Verificar si hay una configuración seleccionada previamente (por errores de validación)
            const initialConfig = document.getElementById('configuration_id').value;
            if (initialConfig) {
                loadConfigurationFields(initialConfig);
            }

            // También cargar cuando cambia la selección
            document.getElementById('configuration_id').addEventListener('change', function() {
                loadConfigurationFields(this.value);
            });
        });
    </script>
@endsection
