@extends('layout.dashboard_layout')
@section('content')
    <!-- start:Page content -->
    <div class="h-full bg-gray-200 p-8">
        <!-- Encabezado con datos fijos -->
        <div class="bg-white rounded-lg shadow-xl pb-6 mb-6">
            <div class="flex flex-col md:flex-row items-center justify-between px-8 py-4">
                <div class="flex items-center space-x-4">
                    <!-- Icono de nacionalidad -->
                    <div
                        class="p-3 rounded-full {{ $resignation->doctor->is_foreign ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                        <svg class="h-8 w-8" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Renuncia #{{ $resignation->id }}</h1>
                        <div class="flex items-center space-x-4 mt-1">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                Médico: {{ $resignation->doctor->ci }}
                            </span>
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $resignation->doctor->is_foreign ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ $resignation->doctor->is_foreign ? 'Extranjero' : 'Nativo' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center space-x-4 mt-4 md:mt-0">
                    <a href="{{ route('doctors.show', $resignation->doctor->id) }}"
                        class="flex items-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm space-x-2 transition duration-100">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <span>Ver Médico</span>
                    </a>
                    <form method="POST" action="{{ route('resignations.destroy', $resignation->id) }}"
                        x-data="{ confirmDelete() { return confirm('¿Estás seguro de eliminar esta renuncia? Esta acción es irreversible.') } }" @submit.prevent="if(confirmDelete()) { $el.submit() }">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="flex items-center bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm space-x-2 transition duration-100">
                            <svg class="h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18 17.94 6M18 18 6.06 6" />
                            </svg>
                            <span>Eliminar Renuncia</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="my-4 flex flex-col lg:flex-row gap-6">
            <!-- Columna izquierda: Datos del médico -->
            <div class="w-full lg:w-2/3 bg-white rounded-lg shadow-xl p-6">
                <h4 class="text-xl text-gray-900 font-bold mb-6">Información del Médico que Renuncia</h4>

                <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
                    <!-- Información básica del médico -->
                    <div class="w-full pb-6 border-b border-gray-200">
                        <h5 class="text-lg font-semibold text-gray-900 mb-4">Datos del Médico</h5>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                                <span class="font-semibold text-blue-700 text-sm uppercase">Cédula</span>
                                <p class="text-blue-900 text-lg font-medium">
                                    {{ $resignation->doctor->ci ?? 'No especificado' }}
                                </p>
                            </div>
                            <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                                <span class="font-semibold text-blue-700 text-sm uppercase">Estado</span>
                                <p class="text-blue-900 text-lg font-medium">
                                    {{ $resignation->doctor->status_name }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Datos del JSON del médico -->
                    @php
                        $data = $resignation->doctor->data ?? [];
                    @endphp

                    @if (count($data) > 0)
                        @foreach ($data as $key => $value)
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-semibold text-gray-700 text-sm uppercase tracking-wide">
                                        {{ ucfirst(str_replace('_', ' ', $key)) }}
                                    </span>
                                </div>
                                <p class="text-gray-900 text-lg font-medium break-words">
                                    {{ $value ?? 'No especificado' }}
                                </p>
                            </div>
                        @endforeach
                    @else
                        <div class="col-span-2 text-center py-8">
                            <svg class="h-12 w-12 text-gray-400 mx-auto" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-gray-500 mt-2">No hay información adicional disponible</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Columna derecha: Edición de la renuncia -->
            <div class="w-full lg:w-1/3 bg-white rounded-lg shadow-xl p-6 h-fit">
                <h4 class="text-xl text-gray-900 font-bold mb-6">Editar Renuncia</h4>

                <!-- Formulario para editar la renuncia -->
                <form action="{{ route('resignations.update', $resignation->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 mb-6">
                        <div class="flex items-center mb-3">
                            <svg class="h-5 w-5 text-gray-600 mr-2" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            <span class="font-semibold text-gray-700">Información de la Renuncia</span>
                        </div>

                        <div class="space-y-4">
                            <!-- Razón (editable) -->
                            <div>
                                <label for="reason" class="font-medium text-gray-700 text-sm">Razón de la Renuncia</label>
                                <input type="text" name="reason" id="reason" required
                                    class="w-full mt-1 p-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none"
                                    placeholder="Ingrese la razón de la renuncia"
                                    value="{{ old('reason', $resignation->reason) }}">
                                @error('reason')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Carta de renuncia -->
                            <div>
                                <label class="font-medium text-gray-700 text-sm">Carta de Renuncia</label>
                                @if ($resignation->resignation_letter)
                                    <div class="mt-2 mb-4">
                                        <a href="{{ asset('storage/' . $resignation->resignation_letter) }}"
                                            target="_blank"
                                            class="inline-flex items-center text-blue-600 hover:text-blue-800">
                                            <svg class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Ver carta actual
                                        </a>
                                    </div>
                                @endif
                                <input type="file" name="resignation_letter" id="resignation_letter"
                                    class="w-full mt-1 p-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none"
                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                <p class="text-xs text-gray-500 mt-1">Formatos aceptados: PDF, DOC, DOCX, JPG, PNG</p>
                                @error('resignation_letter')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Documento adicional -->
                            <div>
                                <label class="font-medium text-gray-700 text-sm">Documento Adicional</label>
                                @if ($resignation->document)
                                    <div class="mt-2 mb-4">
                                        <a href="{{ asset('storage/' . $resignation->document) }}" target="_blank"
                                            class="inline-flex items-center text-blue-600 hover:text-blue-800">
                                            <svg class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Ver documento actual
                                        </a>
                                    </div>
                                @endif
                                <input type="file" name="document" id="document"
                                    class="w-full mt-1 p-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none"
                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                <p class="text-xs text-gray-500 mt-1">Formatos aceptados: PDF, DOC, DOCX, JPG, PNG</p>
                                @error('document')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Información de fechas -->
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 mb-6">
                        <div class="flex items-center mb-3">
                            <svg class="h-5 w-5 text-gray-600 mr-2" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="font-semibold text-gray-700">Fechas de Registro</span>
                        </div>
                        <div class="space-y-2">
                            <p class="text-sm text-gray-600">Creado: {{ $resignation->created_at->format('d/m/Y H:i') }}
                            </p>

                        </div>
                    </div>

                    <!-- Botón de guardar -->
                    <button type="submit"
                        class="w-full bg-primary hover:bg-primary-dark text-white py-2 px-4 rounded-lg transition duration-150">
                        Actualizar Renuncia
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
