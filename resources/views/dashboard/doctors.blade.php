@extends('layout.dashboard_layout')

@section('content')
    <div class="h-full bg-gray-200 p-8">
        <!-- start::Advance Table Filters -->
        <div x-data="{ filter: false }" class="bg-white rounded-lg px-8 py-6 overflow-x-hidden custom-scrollbar">
            <div class="flex justify-between items-center">
                <h4 class="text-xl font-semibold">Médicos</h4>
                <a href="{{ route('doctors.create') }}"
                    class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-lg flex items-center space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z"
                            clip-rule="evenodd" />
                    </svg>
                    <span>Registrar Médico</span>
                </a>
            </div>

            <!-- Filtros alineados a la izquierda -->
            <div class="mt-8 mb-3">
                <div class="flex flex-col space-y-4">
                    <!-- Buscador -->
                    <div class="w-full md:w-2/3">
                        <form method="GET" action="{{ url()->current() }}" class="flex items-center">
                            <div class="relative flex-grow">
                                <input type="search" name="search" id="search"
                                    class="w-full py-2 pl-10 pr-4 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                    placeholder="Buscar por cédula o cualquier dato..." value="{{ request('search') }}">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <button type="submit"
                                class="ml-2 bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-dark">
                                Buscar
                            </button>
                        </form>
                    </div>

                    <!-- Filtros en línea -->
                    <div class="flex flex-col md:flex-row md:items-center space-y-2 md:space-y-0 md:space-x-4">
                        <!-- Filtro de nacionalidad -->
                        <div class="flex items-center">
                            <label class="text-sm font-medium text-gray-700 mr-2 whitespace-nowrap">Nacionalidad:</label>
                            <select name="is_foreign"
                                class="py-2 border-gray-300 rounded-lg focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                onchange="this.form.submit()">
                                <option value="">Todos</option>
                                <option value="0" {{ request('is_foreign') === '0' ? 'selected' : '' }}>Nativo</option>
                                <option value="1" {{ request('is_foreign') === '1' ? 'selected' : '' }}>Extranjero
                                </option>
                            </select>
                        </div>

                        <!-- Filtro de estado -->
                        <div class="flex items-center">
                            <label class="text-sm font-medium text-gray-700 mr-2 whitespace-nowrap">Estado:</label>
                            <select name="status_id"
                                class="py-2 border-gray-300 rounded-lg focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                onchange="this.form.submit()">
                                <option value="">Todos</option>
                                @foreach ($statuses as $status)
                                    <option value="{{ $status['value'] }}"
                                        {{ request('status_id') == $status['value'] ? 'selected' : '' }}>
                                        {{ $status['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Botón limpiar -->
                        <div>
                            <a href="{{ url()->current() }}"
                                class="text-gray-600 hover:text-gray-800 text-sm whitespace-nowrap">
                                Limpiar filtros
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla simplificada -->
            <div class="border border-gray-200 rounded-lg overflow-hidden mt-4">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-16">ID</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-32">Cédula</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Datos</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-28">Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($doctors as $doctor)
                                <tr class="{{ $loop->odd ? 'bg-gray-50' : 'bg-white' }} hover:bg-gray-100">
                                    <td class="px-4 py-3 text-sm text-gray-500">{{ $doctor->id }}</td>

                                    <td class="px-4 py-3 text-sm text-gray-500 truncate">
                                        <div class="flex items-center">
                                            <!-- Icono de nacionalidad -->
                                            <div class="mr-2"
                                                title="{{ $doctor->is_foreign ? 'Extranjero' : 'Nativo' }}">
                                                <svg class="h-4 w-4 {{ $doctor->is_foreign ? 'text-green-500' : 'text-blue-500' }}"
                                                    fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            {{ $doctor->ci ?? 'N/A' }}
                                        </div>
                                    </td>

                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            @php
                                                $data = $doctor->data ?? [];
                                                $excludedFields = ['ci', 'start_date', 'end_date'];
                                                $filteredData = $data;

                                                // Dividir los datos en dos grupos
                                                $totalFields = count($filteredData);
                                                $third = ceil($totalFields / 3);
                                                $firstPart = array_slice($filteredData, 0, $third, true);
                                                $secondPart = array_slice($filteredData, $third, null, true);
                                                $thirdPart = array_slice($filteredData, $third, null, true);

                                                $firstCount = 0;
                                                $secondCount = 0;
                                                $thirdCount = 0;

                                            @endphp

                                            <!-- Primera columna de datos -->
                                            <div class="space-y-2">
                                                @foreach ($firstPart as $key => $value)
                                                    @if ($firstCount < 3)
                                                        <div class="truncate">
                                                            <span class="font-medium text-xs text-gray-500 uppercase">
                                                                {{ ucfirst(str_replace('_', ' ', $key)) }}:
                                                            </span>
                                                            <span class="text-gray-800 block truncate"
                                                                title="{{ $value }}">
                                                                {{ $value }}
                                                            </span>
                                                        </div>
                                                        @php
                                                            $firstCount += 1;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                            </div>

                                            <!-- Segunda columna de datos -->
                                            <div class="space-y-2">
                                                @foreach ($secondPart as $key => $value)
                                                    @if ($secondCount < 3)
                                                        <div class="truncate">
                                                            <span class="font-medium text-xs text-gray-500 uppercase">
                                                                {{ ucfirst(str_replace('_', ' ', $key)) }}:
                                                            </span>
                                                            <span class="text-gray-800 block truncate"
                                                                title="{{ $value }}">
                                                                {{ $value }}
                                                            </span>
                                                        </div>
                                                        @php
                                                            $secondCount += 1;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                            </div>

                                            <!-- Tercera columna de datos -->
                                            <div class="space-y-2">
                                                @foreach ($thirdPart as $key => $value)
                                                    @if ($thirdCount < 3)
                                                        <div class="truncate">
                                                            <span class="font-medium text-xs text-gray-500 uppercase">
                                                                {{ ucfirst(str_replace('_', ' ', $key)) }}:
                                                            </span>
                                                            <span class="text-gray-800 block truncate"
                                                                title="{{ $value }}">
                                                                {{ $value }}
                                                            </span>
                                                        </div>
                                                        @php
                                                            $thirdCount += 1;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>

                                        @if (count($filteredData) === 0)
                                            <div class="text-gray-400 italic">Sin datos adicionales</div>
                                        @endif
                                    </td>

                                    <td class="px-4 py-3 text-sm font-medium">
                                        <div class="flex space-x-[0px]">
                                            <a href="{{ route('doctors.show', $doctor->id) }}"
                                                class="text-blue-600 hover:text-blue-900 p-1 rounded-full hover:bg-blue-100"
                                                title="Ver">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                            <a href="{{ route('doctors.edit', $doctor->id) }}"
                                                class="text-green-600 hover:text-green-900 p-1 rounded-full hover:bg-green-100"
                                                title="Editar">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                            <form action="{{ route('doctors.destroy', $doctor->id) }}" method="POST"
                                                class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-red-600 hover:text-red-900 p-1 rounded-full hover:bg-red-100"
                                                    title="Eliminar"
                                                    onclick="return confirm('¿Estás seguro de eliminar este médico?')">
                                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-4 text-center text-sm text-gray-500">
                                        No se encontraron médicos registrados
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Paginación -->
            <div class="mt-4">
                {{ $doctors->links() }}
            </div>
        </div>
        <!-- end::Advance Table Filters -->
    </div>

    <script>
        // Añadir formulario a los selects para que funcionen con el evento onchange
        document.querySelectorAll('select[name="is_foreign"], select[name="status_id"]').forEach(select => {
            // Crear formulario para cada select
            const form = document.createElement('form');
            form.method = 'GET';
            form.action = window.location.href.split('?')[0];

            // Añadir todos los parámetros de búsqueda existentes
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.forEach((value, key) => {
                if (key !== select.name) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    input.value = value;
                    form.appendChild(input);
                }
            });

            // Insertar el formulario en el DOM y asignarlo al select
            document.body.appendChild(form);
            select.form = form;

            // Añadir el select al formulario
            select.addEventListener('change', function() {
                // Actualizar el valor en el formulario
                if (this.value) {
                    const existingInput = form.querySelector(`input[name="${this.name}"]`);
                    if (existingInput) {
                        existingInput.value = this.value;
                    } else {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = this.name;
                        input.value = this.value;
                        form.appendChild(input);
                    }
                } else {
                    const existingInput = form.querySelector(`input[name="${this.name}"]`);
                    if (existingInput) {
                        form.removeChild(existingInput);
                    }
                }

                // Enviar el formulario
                form.submit();
            });
        });
    </script>
@endsection
