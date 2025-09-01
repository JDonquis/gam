@extends('layout.dashboard_layout')

@section('content')
    <div class="h-full bg-gray-200 p-8">
        <div class="bg-white rounded-lg px-8 py-6">
            <div class="flex justify-between items-center mb-6">
                <h4 class="text-xl font-semibold">Nuevo documento</h4>
                <a href="{{ route('census.index') }}"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg flex items-center space-x-2 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Volver</span>
                </a>
            </div>

            <!-- Sección de carga de archivo -->
            <div x-data="censusData()" class="space-y-6">
                <!-- Selección de configuración -->
                <div class="mb-6">
                    <label for="configuration-select" class="block text-sm font-medium text-gray-700 mb-2">Tipo de
                        Configuración*</label>
                    <select x-model="selectedConfiguration" id="configuration-select" required
                        class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary">
                        <option value="">Seleccione una configuración...</option>
                        @foreach ($configurations as $config)
                            <option value="{{ $config->id }}">{{ $config->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-6">
                    <label for="type-document-select" class="block text-sm font-medium text-gray-700 mb-2">Tipo de
                        Documento*</label>
                    <select x-model="selectedTypeDocument" id="type-document-select" required
                        class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary">
                        <option value="">Seleccione el tipo de documento registrara...</option>
                        <option value="1">Registro de censos</option>
                        <option value="2">Registro de renuncias (No ingresara nuevos doctores, solo buscara
                            incidencias)</option>

                    </select>
                </div>

                <!-- Nombre de la hoja -->
                <div class="mb-6">
                    <label for="sheet-name" class="block text-sm font-medium text-gray-700 mb-2">Nombre de la Hoja*</label>
                    <input x-model="sheetName" type="text" id="sheet-name" required
                        class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary"
                        placeholder="Ingrese el nombre de la hoja del Excel">
                </div>

                <!-- Panel de carga de archivo -->
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                    <template x-if="!file">
                        <div class="space-y-3">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <div class="flex justify-center text-sm text-gray-600">
                                <label for="file-upload"
                                    class="relative cursor-pointer bg-white rounded-md font-medium text-primary hover:text-primary-dark focus-within:outline-none">
                                    <span>Sube un archivo</span>
                                    <input id="file-upload" name="file-upload" type="file" class="sr-only"
                                        @change="handleFileChange($event)" accept=".xlsx,.xls,.csv">
                                </label>
                                <p class="pl-1">o arrástralo aquí</p>
                            </div>
                            <p class="text-xs text-gray-500">Formatos soportados: XLSX, XLS, CSV (hasta 5MB)</p>
                        </div>
                    </template>

                    <template x-if="file">
                        <div class="space-y-4">
                            <div class="flex items-center justify-center space-x-4">
                                <template x-if="previewUrl">
                                    <img :src="previewUrl" class="h-16 w-16 object-cover rounded-md"
                                        alt="Previsualización">
                                </template>
                                <template x-if="!previewUrl">
                                    <div class="h-16 w-16 flex items-center justify-center rounded-md bg-gray-100">
                                        <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                </template>
                                <div class="flex-1 text-left">
                                    <h4 class="text-sm font-medium text-gray-900 truncate" x-text="file.name"></h4>
                                    <p class="text-sm text-gray-500" x-text="(file.size / 1024).toFixed(1) + ' KB'"></p>
                                </div>
                                <button @click="removeFile()" type="button" class="p-1 text-gray-400 hover:text-gray-500">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </template>

                    <!-- Mensajes de error -->
                    <template x-if="errors.length > 0">
                        <div class="mt-4">
                            <div class="bg-red-50 border-l-4 border-red-400 p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-red-700" x-text="errors[0]"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Spinner de carga -->
                <template x-if="isLoading">
                    <div class="flex justify-center py-8">
                        <svg class="animate-spin h-8 w-8 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </div>
                </template>

                <!-- Depuración de datos -->
                <template x-if="tableData.length > 0 && configurationStructure.length > 0">
                    <div>
                        <p class="text-sm text-gray-500">Datos cargados: <span x-text="tableData.length"></span>
                            registros, <span x-text="configurationStructure.length"></span> columnas</p>
                    </div>
                </template>

                <!-- Tabla de previsualización -->
                <template x-if="tableData.length > 0 && requiredFields.length > 0">
                    <div class="overflow-x-auto">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">Previsualización de datos</h3>
                            <span class="bg-gray-100 rounded-full px-3 py-1 text-sm">
                                <span x-text="countValidRecords()"></span> registros encontrados
                            </span>
                        </div>

                        <div class="border border-gray-200 rounded-lg overflow-hidden mb-6 max-h-[500px] overflow-y-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <!-- Mostrar solo los primeros 7 campos requeridos -->
                                        <template x-for="(field, index) in requiredFields.slice(0, 7)"
                                            :key="field.excel_cell">
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                                x-text="field.name"></th>
                                        </template>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Acciones
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <template x-for="(registro, index) in tableData" :key="index">
                                        <tr class="hover:bg-gray-50">
                                            <!-- Mostrar solo los primeros 7 campos requeridos -->
                                            <template x-for="(field, fieldIndex) in requiredFields.slice(0, 7)"
                                                :key="field.excel_cell">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                                    x-text="getCellValue(registro, field.excel_cell)"></td>
                                            </template>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <button @click="editarRegistro(registro, index)"
                                                    class="text-blue-600 hover:text-blue-900 mr-3" title="Editar">
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                    </svg>
                                                </button>
                                                <button @click="eliminarRegistro(index)"
                                                    class="text-red-600 hover:text-red-900" title="Eliminar">
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        <!-- Modal de edición -->
                        <template x-if="edicionActiva && registroEditando">
                            <div class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center p-4 z-50">
                                <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                                    <div class="p-6">
                                        <div class="flex justify-between items-center mb-4">
                                            <h3 class="text-lg font-medium">Editar Registro</h3>
                                            <button @click="cancelarEdicion()" class="text-gray-500 hover:text-gray-700">
                                                <svg class="h-6 w-6" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <template x-for="field in configurationStructure" :key="field.excel_cell">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-2"
                                                        x-text="field.name + (field.required ? '*' : '')"></label>
                                                    <input type="text" x-model="registroEditando[field.excel_cell]"
                                                        :required="field.required"
                                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary"
                                                        :placeholder="'Ingrese ' + field.name.toLowerCase()">
                                                </div>
                                            </template>
                                        </div>

                                        <div class="mt-6 flex justify-end space-x-3">
                                            <button @click="cancelarEdicion()" type="button"
                                                class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg text-sm">
                                                Cancelar
                                            </button>
                                            <button @click="guardarEdicion()" type="button"
                                                class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-lg text-sm">
                                                Guardar Cambios
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <div class="mt-6 flex justify-end space-x-3">
                            <button @click="removeFile()" type="button"
                                class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg text-sm">
                                Cancelar
                            </button>
                            <button type="button" @click="confirmarYGuardar()"
                                class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-lg text-sm">
                                Confirmar y Guardar
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <script>
        function censusData() {
            return {
                file: null,
                previewUrl: null,
                isLoading: false,
                tableData: [],
                configurationStructure: [],
                requiredFields: [],
                errors: [],
                registroSeleccionado: null,
                edicionActiva: false,
                registroEditando: null,
                selectedConfiguration: null,
                selectedTypeDocument: null,
                sheetName: '',
                configurationData: @json($configurations),

                handleFileChange(event) {
                    this.errors = [];
                    const selectedFile = event.target.files[0];

                    if (!selectedFile) return;

                    // Validar tipo de archivo
                    const validTypes = ['application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'text/csv'
                    ];
                    if (!validTypes.includes(selectedFile.type)) {
                        this.errors.push('Solo se permiten archivos Excel o CSV');
                        return;
                    }

                    // Validar tamaño (max 5MB)
                    if (selectedFile.size > 5 * 1024 * 1024) {
                        this.errors.push('El archivo no debe superar los 5MB');
                        return;
                    }

                    // Validar configuración y nombre de la hoja
                    if (!this.selectedConfiguration) {
                        this.errors.push('Debe seleccionar una configuración');
                        return;
                    }

                    if (!this.selectedTypeDocument) {
                        this.errors.push('Debe seleccionar un tipo de documento');
                        return;
                    }

                    if (!this.sheetName.trim()) {
                        this.errors.push('Debe ingresar el nombre de la hoja');
                        return;
                    }

                    this.file = selectedFile;
                    this.previewUrl = selectedFile.type.startsWith('image/') ? URL.createObjectURL(selectedFile) : null;
                    this.uploadFile();
                },

                async uploadFile() {
                    if (!this.file || !this.selectedConfiguration || !this.selectedTypeDocument || !this.sheetName
                        .trim()) return;

                    this.isLoading = true;
                    this.errors = [];

                    try {
                        const formData = new FormData();
                        formData.append('file', this.file);
                        formData.append('configuration_id', this.selectedConfiguration);
                        formData.append('sheet_name', this.sheetName);
                        formData.append('type_document_id', this.selectedTypeDocument);

                        const response = await fetch('{{ route('census.preview') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: formData
                        });

                        if (!response.ok) {
                            const errorData = await response.json();
                            throw new Error(errorData.message || 'Error al procesar el archivo');
                        }

                        const data = await response.json();
                        console.log('Respuesta del servidor:', data);

                        // Obtener la estructura de la configuración seleccionada primero
                        const selectedConfig = this.configurationData.find(config => config.id == this
                            .selectedConfiguration);
                        if (selectedConfig) {
                            this.configurationStructure = selectedConfig.structure || [];
                            console.log('Estructura actualizada:', this.configurationStructure);
                        }

                        // Calcular la fila inicial máxima
                        const rowNumbers = this.configurationStructure.map(field => {
                            const rowMatch = field.excel_cell.match(/\d+$/);
                            return rowMatch ? parseInt(rowMatch[0]) : 0;
                        });
                        const maxRow = Math.max(...rowNumbers);

                        console.log('Fila inicial máxima:', maxRow);

                        // Asegurar que tableData sea un array de filas a partir de la fila máxima
                        let rawData = data.registers[0];
                        console.log('rawData:', rawData);
                        let rows = Object.values(rawData); // Convertir a array

                        // Filtrar rows a partir de la fila máxima (asumiendo que rows[0] es fila 1)
                        rows = rows.slice(maxRow); // Si maxRow es 9, slice(8) para empezar desde fila 9 (índice 8)

                        console.log('rows filtradas:', rows);

                        // Normalizar las claves de tableData para coincidir con configurationStructure
                        this.tableData = rows.map((registro, index) => {
                            const normalizedRegistro = {};
                            Object.keys(registro).forEach(key => {
                                // Mapear claves simples (A, B, C) a claves con sufijo (B8, C8, etc.)
                                const matchingField = this.configurationStructure.find(field =>
                                    field.excel_cell.replace(/[0-9]/g, '') === key
                                );
                                if (matchingField) {
                                    normalizedRegistro[matchingField.excel_cell] = registro[key];
                                } else {
                                    normalizedRegistro[key] = registro[key];
                                }
                            });
                            // Agregar un identificador único para cada fila
                            normalizedRegistro._rowIndex = index;
                            return normalizedRegistro;
                        });

                        console.log('tableData normalizado:', this.tableData);

                        // Filtrar los campos requeridos para la tabla
                        this.requiredFields = this.configurationStructure.filter(field => field.required);

                    } catch (error) {
                        this.errors.push(error.message);
                        console.error('Error en uploadFile:', error);
                    } finally {
                        this.isLoading = false;
                    }
                },

                removeFile() {
                    this.file = null;
                    this.previewUrl = null;
                    this.tableData = [];
                    this.configurationStructure = [];
                    this.requiredFields = [];
                    this.errors = [];
                    this.registroSeleccionado = null;
                    this.selectedConfiguration = null;
                    this.selectedTypeDocument = null;
                    this.sheetName = '';
                },

                getCellValue(registro, cell) {
                    return registro[cell] || '';
                },

                editarRegistro(registro, index) {
                    this.registroEditando = {
                        ...registro,
                        originalIndex: index
                    };
                    this.edicionActiva = true;
                },

                eliminarRegistro(index) {
                    if (confirm('¿Está seguro de que desea eliminar este registro?')) {
                        this.tableData.splice(index, 1);
                    }
                },

                guardarEdicion() {
                    if (!this.registroEditando) return;

                    // Validar campos requeridos
                    const requiredFields = this.configurationStructure.filter(field => field.required);
                    for (const field of requiredFields) {
                        const value = this.registroEditando[field.excel_cell];
                        if (!value || value.toString().trim() === '') {
                            alert(`El campo ${field.name} es obligatorio`);
                            return;
                        }
                    }

                    // Actualizar el registro en la tabla
                    this.tableData[this.registroEditando.originalIndex] = {
                        ...this.registroEditando
                    };
                    this.edicionActiva = false;
                    this.registroEditando = null;
                    alert('Cambios guardados exitosamente');
                },

                cancelarEdicion() {
                    this.edicionActiva = false;
                    this.registroEditando = null;
                },

                countValidRecords() {
                    if (!this.tableData || this.tableData.length === 0) return 0;

                    // Contar registros que tengan al menos un campo con valor
                    return this.tableData.filter(registro => {
                        return Object.values(registro).some(value =>
                            value !== null && value !== undefined && value.toString().trim() !== ''
                        );
                    }).length;
                },

                async confirmarYGuardar() {
                    if (!confirm('¿Está seguro que desea guardar los datos?')) {
                        return;
                    }

                    if (this.tableData.length === 0) {
                        alert('No hay datos para guardar');
                        return;
                    }

                    try {
                        this.isLoading = true;

                        const formData = new FormData();
                        if (this.file) {
                            formData.append('file', this.file);
                        }
                        formData.append('configuration_id', this.selectedConfiguration);
                        formData.append('type_document_id', this.selectedTypeDocument);
                        formData.append('sheet_name', this.sheetName);
                        // Enviar tableData sin el campo _rowIndex
                        const cleanedTableData = this.tableData.map(registro => {
                            const {
                                _rowIndex,
                                ...cleanedRegistro
                            } = registro;
                            return cleanedRegistro;
                        });
                        formData.append('registros', JSON.stringify(cleanedTableData));

                        const response = await fetch('{{ route('census.store') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: formData
                        });

                        if (!response.ok) {
                            const errorData = await response.json();
                            throw new Error(errorData.message || 'Error al guardar los datos');
                        }

                        const data = await response.json();

                        if (data.success) {
                            sessionStorage.setItem('flashMessage', data.message);
                            window.location.href = data.redirect;
                        }

                    } catch (error) {
                        this.errors.push(error.message);
                        console.error('Error en confirmarYGuardar:', error);
                    } finally {
                        this.isLoading = false;
                    }
                }
            };
        }
    </script>
@endsection
