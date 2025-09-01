@extends('layout.dashboard_layout')
@section('content')

    @php
        $structure = $incidence->censusData->census->configuration->structure ?? [];
        $censusData = $incidence->censusData->data ?? [];
        $doctorData = $incidence->doctor->data ?? [];
    @endphp

    <div class="h-full bg-gray-200 p-8" x-data="{
        isForeign: {{ $incidence->censusData->is_foreign ? 'true' : 'false' }},
        showResolutionModal: false,
        resolutionType: '',
        showSanctionModal: false,
    
        toggleForeign() {
            this.isForeign = !this.isForeign;
            document.getElementById('is_foreign').checked = this.isForeign;
        },
    
        openModal(type) {
            this.resolutionType = type;
            this.showResolutionModal = true;
        },
    
        submitForm(actionType) {
            if (actionType === 'delete') {
                // Enviar formulario de eliminación
                document.getElementById('deleteIncidenceForm').submit();
            } else if (actionType === 'update') {
                document.getElementById('incidenceForm').submit();
            }
        }
    }">
        <div class="m-auto bg-white p-4 max-w-8xl flex justify-center gap-8 rounded-lg shadow-xl py-8 mt-12">
            <div class="{{ $incidence->doctor ? 'w-2/3' : 'w-full' }}">
                <h4 class="text-xl capitalize">Editar Información de la Incidencia</h4>

                <!-- Mostrar el motivo de la incidencia -->
                <div class="mt-4 p-4 bg-yellow-50 border-l-4 border-yellow-500 rounded">
                    <h5 class="font-semibold text-yellow-800">Motivo de la Incidencia:</h5>
                    <p class="mt-2 text-gray-700">{{ $incidence->reason }}</p>
                </div>

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

                    <form action="{{ route('incidences.update', $incidence->id) }}" method="POST"
                        enctype="multipart/form-data" id="incidenceForm">
                        @csrf
                        @method('PUT')

                        <!-- Checkbox para extranjero -->
                        <div class="mt-4 mb-6">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="data[is_foreign]" id="is_foreign"
                                    class="rounded border-gray-300 text-primary focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                    value="1" x-model="isForeign">
                                <span class="ml-2 text-gray-700">Es extranjero</span>
                            </label>
                        </div>

                        <!-- Mostrar campos según la estructura de configuración en 2 columnas -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            @foreach ($structure as $field)
                                @php
                                    $fieldKey = $field['name'];
                                    $fieldValue = $censusData[$fieldKey] ?? '';
                                    $doctorValue = $doctorData[$fieldKey] ?? '';
                                    $isSameValue = $incidence->doctor && $doctorValue && $doctorValue == $fieldValue;
                                @endphp

                                <div class="flex flex-col">
                                    <label for="data_{{ $fieldKey }}" class="text-gray-700">
                                        {{ $field['name'] }}
                                        @if ($field['required'])
                                            *
                                        @endif
                                    </label>

                                    <input type="text" name="data[{{ $fieldKey }}]" id="data_{{ $fieldKey }}"
                                        class="flex-1 py-1 border-gray-300 mt-1 rounded focus:border-gray-300 focus:outline-none focus:ring-0 {{ $isSameValue ? 'border-red-400 bg-red-50' : '' }}"
                                        value="{{ old('data.' . $fieldKey, $fieldValue) }}"
                                        {{ $field['required'] ? 'required' : '' }}>

                                    <!-- Mostrar valor del doctor si existe y coincide -->
                                    @if ($isSameValue)
                                        <p class="text-sm text-red-600 mt-1">
                                            ⚠️ Coincide con el sistema: {{ $doctorValue }}
                                        </p>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <!-- Campo de observación -->
                        <div class="mt-6">
                            <div class="flex flex-col">
                                <label for="observation" class="text-gray-700">Observación</label>
                                <textarea name="observation" id="observation"
                                    class="flex-1 py-1 border-gray-300 mt-1 rounded focus:border-gray-300 focus:outline-none focus:ring-0"
                                    rows="3">{{ old('observation', $incidence->censusData->observation ?? '') }}</textarea>
                            </div>
                        </div>

                        <!-- Botones de acción -->
                        <div class="flex gap-2 mt-6">
                            <a href="{{ route('incidences.index') }}" type="button"
                                class="bg-secondary bg-opacity-20 hover:bg-opacity-40 rounded-lg px-6 py-1.5 text-secondary hover:shadow-xl transition duration-150">Cancelar</a>
                            <button type="button" @click="openModal('update')"
                                class="bg-primary hover:bg-primary-dark rounded-lg px-6 py-1.5 text-gray-100 hover:shadow-xl transition duration-150">Resolver
                                Incidencia</button>
                        </div>
                    </form>

                    <!-- Formulario oculto para eliminar la incidencia -->
                    <form id="deleteIncidenceForm" action="{{ route('incidences.destroy', $incidence->id) }}"
                        method="POST" class="hidden">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="resolution_type" value="delete">
                    </form>
                </div>
            </div>

            @if ($incidence->doctor)
                <div class="w-1/3">
                    <div class="bg-white p-4 rounded-lg shadow">
                        <h4 class="text-xl font-semibold mb-4">Datos del Médico en el Sistema</h4>

                        <div class="space-y-6">
                            <!-- Campos del doctor en 2 columnas -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach ($doctorData as $key => $value)
                                    @if (!empty($value))
                                        @php
                                            $censusValue = $censusData[$key] ?? '';
                                            $isSameValue = $censusValue && $value == $censusValue;
                                            $fieldName = $key;

                                            // Buscar el nombre bonito en la estructura
                                            foreach ($structure as $field) {
                                                if ($field['name'] === $key) {
                                                    $fieldName = $field['name'];
                                                    break;
                                                }
                                            }
                                        @endphp

                                        <div
                                            class="p-3 rounded-lg {{ $isSameValue ? 'bg-red-50 border border-red-200' : 'bg-gray-50' }}">
                                            <h6 class="font-medium text-gray-700 text-sm">{{ $fieldName }}</h6>
                                            <p class="mt-1 text-gray-800">{{ $value }}</p>

                                            @if ($isSameValue)
                                                <p class="text-sm text-red-600 mt-1">
                                                    ⚠️ Coincide con la incidencia
                                                </p>
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                            </div>

                            <!-- Botones de acción -->
                            <div class="mt-6 flex flex-col gap-2">
                                <a href="{{ route('doctors.edit', $incidence->doctor->id) }}"
                                    class="bg-blue-600 hover:bg-blue-700 rounded-lg px-4 py-2 text-white text-center transition duration-150">
                                    Editar médico
                                </a>

                                <!-- Botón de sanción con formulario POST -->
                                <form action="{{ route('sanctions.store') }}" method="POST" class="w-full">
                                    @csrf
                                    <input type="hidden" name="doctor_id" value="{{ $incidence->doctor->id }}">
                                    <input type="hidden" name="incidence_id" value="{{ $incidence->id }}">
                                    <input type="hidden" name="reason"
                                        value="Sanción por incidencia #{{ $incidence->id }} - {{ $incidence->reason }}">
                                    <button type="submit"
                                        class="w-full bg-red-600 hover:bg-red-700 rounded-lg px-4 py-2 text-white text-center transition duration-150">
                                        Sancionar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Modal de Resolución -->
        <div x-show="showResolutionModal" x-cloak
            class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl p-6 m-4 max-w-xl w-full" @click.away="showResolutionModal = false">
                <h3 class="text-xl font-semibold mb-4"
                    x-text="resolutionType === 'update' ? 'Actualizar Censo' : 'Eliminar Incidencia'"></h3>

                <template x-if="resolutionType === 'update'">
                    <div>
                        <p class="text-gray-700 mb-4">¿Cómo resolverá la incidencia?</p>
                        <div class="flex gap-3 justify-end">
                            <button type="button" @click="showResolutionModal = false"
                                class="bg-gray-300 hover:bg-gray-400 rounded-lg px-4 py-2 text-gray-800 transition duration-150">
                                Cancelar
                            </button>
                            <button type="button" @click="submitForm('delete')"
                                class="bg-secondary hover:bg-secondary-dark rounded-lg px-4 py-2 text-white transition duration-150">
                                Solo eliminar la incidencia
                            </button>
                            @if ($incidence->censusData->census->type_document_id == 1)
                                <button type="button" @click="submitForm('update')"
                                    class="bg-primary hover:bg-primary-dark rounded-lg px-4 py-2 text-white transition duration-150">
                                    Actualizar e reintentar
                                </button>
                            @endif
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

@endsection
