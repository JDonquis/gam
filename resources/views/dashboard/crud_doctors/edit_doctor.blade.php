@extends('layout.dashboard_layout')
@section('content')

    <div class="h-full bg-gray-200 p-8">
        <div class="m-auto bg-white p-4 max-w-6xl flex justify-center gap-8 rounded-lg shadow-xl py-8 mt-12">
            <div class="w-full">
                <h4 class="text-xl capitalize">Editar Información del Médico</h4>
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
                    <form action="{{ route('doctors.update', $doctor->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- SECCIÓN 1: Cédula y Nacionalidad -->
                        <div class="bg-gray-50 p-6 rounded-lg mb-6">
                            <h5 class="text-lg font-semibold text-gray-800 mb-4">1. Identificación y Nacionalidad</h5>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="flex flex-col">
                                    <label for="ci" class="text-gray-700">Cédula*</label>
                                    <input type="text" name="ci" id="ci"
                                        class="flex-1 py-2 border-gray-300 mt-1 rounded focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                                        placeholder="Ej: 12345678" value="{{ old('ci', $doctor->ci) }}" required>
                                </div>

                                <div class="flex flex-col justify-center">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="is_foreign" id="is_foreign"
                                            class="rounded border-gray-300 text-primary focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                            value="1" {{ old('is_foreign', $doctor->is_foreign) ? 'checked' : '' }}>
                                        <span class="ml-2 text-gray-700 font-medium">Es extranjero</span>
                                    </label>
                                    <p class="text-sm text-gray-500 mt-1">Marcar si el médico es extranjero</p>
                                </div>
                            </div>
                        </div>

                        <!-- SECCIÓN 2: Datos del JSON -->
                        <div class="bg-gray-50 p-6 rounded-lg mb-6">
                            <h5 class="text-lg font-semibold text-gray-800 mb-4">2. Información Adicional</h5>

                            @php
                                $data = $doctor->data ?? [];
                                $excludedFields = ['ci', 'start_date', 'end_date']; // Campos a excluir
                            @endphp

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @if (count($data) > 0)
                                    @foreach ($data as $key => $value)
                                        @if (!in_array($key, $excludedFields))
                                            <div class="flex flex-col">
                                                <label for="data_{{ $key }}" class="text-gray-700 capitalize">
                                                    {{ str_replace('_', ' ', $key) }}
                                                </label>
                                                <input type="text" name="data[{{ $key }}]"
                                                    id="data_{{ $key }}"
                                                    class="flex-1 py-2 border-gray-300 mt-1 rounded focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                                                    placeholder="Ingrese {{ str_replace('_', ' ', $key) }}"
                                                    value="{{ old("data.$key", $value) }}">
                                            </div>
                                        @endif
                                    @endforeach
                                @else
                                    <div class="col-span-2 text-center py-4 text-gray-500">
                                        No hay datos adicionales configurados
                                    </div>
                                @endif
                            </div>


                        </div>

                        <!-- SECCIÓN 3: Información del Curso -->
                        <div class="bg-gray-50 p-6 rounded-lg mb-6">
                            <h5 class="text-lg font-semibold text-gray-800 mb-4">3. Información del Curso</h5>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="flex flex-col">
                                    <label for="data_start_date" class="text-gray-700">Fecha de Inicio del Curso*</label>
                                    <input type="date" name="start_date" id="data_start_date"
                                        class="flex-1 py-2 border-gray-300 mt-1 rounded focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                                        value="{{ old('start_date', $doctor->course->start_date->format('Y-m-d') ?? '') }}"
                                        required>
                                </div>

                                <div class="flex flex-col">
                                    <label for="data_end_date" class="text-gray-700">Fecha de Fin del Curso*</label>
                                    <input type="date" name="end_date" id="data_end_date"
                                        class="flex-1 py-2 border-gray-300 mt-1 rounded focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                                        value="{{ old('end_date', $doctor->course->end_date->format('Y-m-d') ?? '') }}"
                                        required>
                                </div>
                            </div>
                        </div>



                        <!-- Botones de acción -->
                        <div class="flex gap-2 mt-8">
                            <a href="{{ route('doctors.index') }}" type="button"
                                class="bg-secondary bg-opacity-20 hover:bg-opacity-40 rounded-lg px-6 py-2 text-secondary hover:shadow-xl transition duration-150">
                                Cancelar
                            </a>
                            <button type="submit"
                                class="bg-primary hover:bg-primary-dark rounded-lg px-6 py-2 text-white hover:shadow-xl transition duration-150">
                                Actualizar Médico
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
