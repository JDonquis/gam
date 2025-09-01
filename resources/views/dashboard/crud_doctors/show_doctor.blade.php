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
                        class="p-3 rounded-full {{ $doctor->is_foreign ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                        <svg class="h-8 w-8" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Cedula: {{ $doctor->ci }}</h1>
                        <div class="flex items-center space-x-4 mt-1">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $doctor->is_foreign ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ $doctor->is_foreign ? 'Extranjero' : 'Nativo' }}
                            </span>

                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @switch($doctor->status)
                                    @case(1) bg-green-100 text-green-800 @break
                                    @case(2) bg-blue-100 text-blue-800 @break
                                    @case(3) bg-yellow-100 text-yellow-800 @break
                                    @case(4) bg-red-100 text-red-800 @break
                                @endswitch">
                                {{ $doctor->status_name }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center space-x-4 mt-4 md:mt-0">
                    <a href="{{ route('doctors.edit', ['doctor' => $doctor->id]) }}"
                        class="flex items-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm space-x-2 transition duration-100">
                        <svg class="h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.779 17.779 4.36 19.918 6.5 13.5m4.279 4.279 8.364-8.643a3.027 3.027 0 0 0-2.14-5.165 3.03 3.03 0 0 0-2.14.886L6.5 13.5m4.279 4.279L6.499 13.5m2.14 2.14 6.213-6.504M12.75 7.04 17 11.28" />
                        </svg>
                        <span>Actualizar</span>
                    </a>
                    <form method="POST" action="{{ route('doctors.destroy', $doctor->id) }}" x-data="{ confirmDelete() { return confirm('¿Estás seguro de eliminar este medico? Esta acción es irreversible.') } }"
                        @submit.prevent="if(confirmDelete()) { $el.submit() }">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="flex items-center bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm space-x-2 transition duration-100">
                            <svg class="h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18 17.94 6M18 18 6.06 6" />
                            </svg>
                            <span>Eliminar</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="my-4 flex flex-col lg:flex-row gap-6">
            <!-- Columna izquierda: Datos del JSON -->
            <div class="w-full lg:w-2/3 bg-white rounded-lg shadow-xl p-6">
                <h4 class="text-xl text-gray-900 font-bold mb-6">Información del Médico</h4>

                <div class="grid grid-cols-1 md:grid-cols-1 gap-6">

                    <div class="w-full pb-6 border-b border-gray-200">
                        <h5 class="text-lg font-semibold text-gray-900 mb-4">Duracion del curso</h5>
                        <div class="grid grid-cols-2 md:grid-cols-2 gap-4">
                            <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                                <span class="font-semibold text-blue-700 text-sm uppercase">Fecha Inicio</span>
                                <p class="text-blue-900 text-lg font-medium">
                                    {{ $doctor->course->start_date->format('d F Y') ?? 'No especificado' }}
                                </p>
                            </div>
                            <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                                <span class="font-semibold text-blue-700 text-sm uppercase">Fecha Fin</span>
                                <p class="text-blue-900 text-lg font-medium">
                                    {{ $doctor->course->end_date->format('d F Y') ?? 'No especificado' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    @php
                        $data = $doctor->data ?? [];
                    @endphp

                    @if (count($data) > 0)
                        @foreach ($data as $key => $value)
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-semibold text-gray-700 text-sm uppercase tracking-wide">
                                        {{ ucfirst(str_replace('_', ' ', $key)) }}
                                    </span>
                                    <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">
                                        {{ $loop->iteration }}/{{ count($data) }}
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

                <!-- Campos fijos importantes -->

            </div>

            <!-- Columna derecha: Estadísticas e información adicional -->
            <div class="w-full lg:w-1/3 bg-white rounded-lg shadow-xl p-6 h-fit">
                <h4 class="text-xl text-gray-900 font-bold mb-6">Estadísticas e Información</h4>



                <!-- Fechas importantes -->
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 mb-6">
                    <div class="flex items-center mb-3">
                        <svg class="h-5 w-5 text-gray-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span class="font-semibold text-gray-700">Fechas de Registro</span>
                    </div>
                    <div class="space-y-2">
                        <p class="text-sm text-gray-600">Creado: {{ $doctor->created_at->format('d/m/Y') }}</p>
                        <p class="text-sm text-gray-600">Actualizado: {{ $doctor->updated_at->format('d/m/Y') }}</p>
                    </div>
                </div>

                <!-- Estadísticas -->
                <div class="grid grid-cols-1 gap-4">
                    <div class="px-4 py-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <div class="flex items-center justify-between mb-3">
                            <span class="font-semibold text-sm text-yellow-700">Incidencias</span>
                            <svg class="w-5 h-5 text-yellow-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="text-center">
                            <span class="text-2xl font-bold text-yellow-700">{{ $incidences }}</span>
                        </div>
                    </div>

                    <div class="px-4 py-4 bg-red-50 border border-red-200 rounded-lg">
                        <div class="flex items-center justify-between mb-3">
                            <span class="font-semibold text-sm text-red-700">Sanciones</span>
                            <svg class="w-5 h-5 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                            </svg>
                        </div>
                        <div class="text-center">
                            <span class="text-2xl font-bold text-red-700">{{ $sanctions }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
