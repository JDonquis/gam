@extends('layout.dashboard_layout')

@section('content')
    <div class="h-full bg-gray-200 p-8">
        <div x-data="{ filter: false }" class="bg-white rounded-lg px-8 py-6">
            <!-- Header con información del censo -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h4 class="text-xl font-semibold">Documento: <span class="text-primary"> {{ $census->title }} </span></h4>
                    <p class="text-sm text-gray-600 mt-1">
                        Creado por: {{ $census->user->fullname }}
                        el {{ $census->created_at->format('d/m/Y H:i') }}
                    </p>
                    <p class="text-sm text-gray-600">
                        Configuración usada: {{ $census->configuration->name ?? 'N/A' }}
                    </p>
                    <p class="text-sm text-gray-600">
                        Total registros: {{ $data->total() }}
                    </p>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-full">
                        {{ $census->is_completed ? 'Completado' : 'En progreso' }}: {{ $census->percentage }}%
                    </span>
                    <a href="{{ route('census.index') }}"
                        class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        <span>Volver</span>
                    </a>
                </div>
            </div>

            @php
                $structure = $census->configuration->structure;
                $headers = [];
                foreach ($structure as $field) {
                    $headers[] = [
                        'name' => $field['name'],
                        'excel_cell' => $field['excel_cell'],
                        'searchable' => $field['searchable'],
                        'filterable' => $field['filterable'],
                    ];
                }
            @endphp

            <!-- Filtros y búsqueda -->
            <div class="mt-8 mb-3 flex flex-col md:flex-row items-start md:items-center md:justify-between">
                <div class="flex items-center justify-center space-x-4">
                    <!-- Buscador general -->
                    <form class="relative flex items-center" method="GET" action="{{ url()->current() }}">
                        <input type="search" name="search" id="search"
                            class="flex-1 py-0.5 pl-10 border-gray-300 rounded focus:outline-none focus:ring-0 focus:border-gray-300"
                            placeholder="Buscar en todos los campos..." value="{{ request('search') }}">
                        <button type="submit" class="absolute left-2 bg-transparent flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                        <!-- Mantener otros filtros -->
                        @if (request()->has('has_observation'))
                            <input type="hidden" name="has_observation" value="{{ request('has_observation') }}">
                        @endif
                    </form>

                    <!-- Filtro de observaciones -->
                    <form method="GET" action="{{ url()->current() }}" class="flex items-center">
                        <select name="has_observation" onchange="this.form.submit()"
                            class="py-0.5 border-gray-300 rounded focus:border-gray-300 focus:outline-none focus:ring-0 ml-2">
                            <option value="">Todas las observaciones</option>
                            <option value="with" {{ request('has_observation') == 'with' ? 'selected' : '' }}>Con
                                observaciones</option>
                            <option value="without" {{ request('has_observation') == 'without' ? 'selected' : '' }}>Sin
                                observaciones</option>
                        </select>
                        <!-- Mantener búsqueda si existe -->
                        @if (request()->has('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif
                    </form>

                    <button @click="filter = !filter"
                        class="text-primary hover:text-primary-dark font-semibold hover:underline">Filtros
                        avanzados</button>
                </div>
            </div>

            <!-- Filtros avanzados -->
            <div x-show="filter" x-collapse.duration.300ms>
                <div class="mb-2 py-4 bg-gray-200 px-8 rounded-lg">
                    <h5 class="mb-4">Filtros avanzados</h5>
                    <form method="GET" action="{{ url()->current() }}">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 my-4">
                            <!-- Búsqueda -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Búsqueda general</label>
                                <input type="text" name="search"
                                    class="w-full py-1 border-gray-300 rounded focus:border-gray-300 focus:outline-none focus:ring-0"
                                    placeholder="Buscar en todos los campos..." value="{{ request('search') }}">
                            </div>

                            <!-- Filtro observaciones -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                                <select name="has_observation"
                                    class="w-full py-1 border-gray-300 rounded focus:border-gray-300 focus:outline-none focus:ring-0">
                                    <option value="">Todas las observaciones</option>
                                    <option value="with" {{ request('has_observation') == 'with' ? 'selected' : '' }}>Con
                                        observaciones</option>
                                    <option value="without"
                                        {{ request('has_observation') == 'without' ? 'selected' : '' }}>Sin observaciones
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="flex space-x-2">
                            <button type="submit"
                                class="bg-primary hover:bg-primary-dark rounded-lg px-6 py-2 text-white text-sm">
                                Aplicar filtros
                            </button>
                            <a href="{{ url()->current() }}"
                                class="bg-gray-500 hover:bg-gray-600 rounded-lg px-6 py-2 text-white text-sm">
                                Limpiar filtros
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabla de datos del censo -->
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>

                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-12">#</th>

                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-32">
                                    {{-- Observaciones  --}}
                                </th>

                                @foreach ($headers as $header)
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        {{ $header['name'] }}
                                    </th>
                                @endforeach

                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($data as $index => $item)
                                @php
                                    $itemData = $item->data;
                                @endphp
                                <tr class="{{ $loop->odd ? 'bg-gray-50' : 'bg-white' }} hover:bg-gray-100">

                                    <td class="px-4 py-3 text-sm text-gray-500">
                                        {{ ($data->currentPage() - 1) * $data->perPage() + $index + 1 }}</td>

                                    <td class="px-4 py-3 text-sm text-gray-500">
                                        @if ($item->observation)
                                            <span class="text-yellow-600" title="{{ $item->observation }}">
                                                ⚠️ {{ Str::limit($item->observation, 50) }}
                                            </span>
                                        @else
                                            <span class="text-green-600">✓</span>
                                        @endif
                                    </td>


                                    @foreach ($headers as $header)
                                        <td class="px-4 py-3 text-sm text-gray-500">
                                            @if (isset($itemData[$header['name']]))
                                                {{ $itemData[$header['name']] ?? 'N/A' }}
                                            @else
                                                <span class="text-gray-400">N/A</span>
                                            @endif
                                        </td>
                                    @endforeach


                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ count($headers) + 2 }}"
                                        class="px-4 py-4 text-center text-sm text-gray-500">
                                        No se encontraron registros en este censo
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Paginación -->
            <div class="mt-4">
                {{ $data->appends(request()->except('page'))->links() }}
            </div>
        </div>
    </div>
@endsection
