@extends('layout.dashboard_layout')

@section('content')
    <div class="h-full bg-gray-200 p-8">
        <div x-data="{ filter: false }" class="bg-white rounded-lg px-8 py-6">
            <div class="flex justify-between items-center mb-6">
                <h4 class="text-xl font-semibold">Configuraciones</h4>
                <a href="{{ route('configuration.create') }}"
                    class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-lg flex items-center space-x-2 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z"
                            clip-rule="evenodd" />
                    </svg>
                    <span>Crear Configuración</span>
                </a>
            </div>

            <!-- Filtros y búsqueda -->
            <div class="mt-8 mb-3 flex flex-col md:flex-row items-start md:items-center md:justify-between">
                <div class="flex items-center justify-center space-x-4">
                    <form class="relative flex items-center" method="GET" action="{{ url()->current() }}">
                        <input type="search" name="search" id="search"
                            class="flex-1 py-0.5 pl-10 border-gray-300 focus:outline-none focus:ring-0 focus:border-gray-300"
                            placeholder="Buscar por nombre..." value="{{ request('search') }}">
                        <button type="submit" class="absolute left-2 bg-transparent flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
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
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 my-4">
                            <input type="text" name="name"
                                class="flex-1 py-1 border-gray-300 rounded focus:border-gray-300 focus:outline-none focus:ring-0"
                                placeholder="Nombre de configuración" value="{{ request('name') }}">
                            <input type="date" name="start_date"
                                class="flex-1 py-1 border-gray-300 rounded focus:border-gray-300 focus:outline-none focus:ring-0"
                                placeholder="Rango inicial" value="{{ request('start_date') }}">
                            <input type="date" name="end_date"
                                class="flex-1 py-1 border-gray-300 rounded focus:border-gray-300 focus:outline-none focus:ring-0"
                                placeholder="Rango final" value="{{ request('end_date') }}">
                        </div>
                        <button type="submit"
                            class="bg-primary hover:bg-primary-dark rounded-lg px-8 py-1 text-gray-100 hover:shadow-xl transition duration-150 mt-4 text-sm">
                            Aplicar filtros
                        </button>
                        <a href="{{ url()->current() }}" class="ml-4 text-gray-600 hover:text-gray-800 text-sm">
                            Limpiar filtros
                        </a>
                    </form>
                </div>
            </div>

            <!-- Tabla compacta -->
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-12">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-32">Fecha Creación
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-28">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($configurations as $config)
                            <tr class="{{ $loop->odd ? 'bg-gray-50' : 'bg-white' }} hover:bg-gray-100">
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $config->id }}</td>

                                <td class="px-4 py-3">
                                    <div class="text-sm font-medium text-gray-900 truncate" title="{{ $config->name }}">
                                        {{ $config->name }}
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        Campos en estructura: {{ count($config->structure) }}
                                    </div>
                                </td>

                                <td class="px-4 py-3 text-sm text-gray-500"
                                    title="{{ $config->created_at->format('d/m/Y H:i') }}">
                                    {{ $config->created_at->format('M j, Y') }}
                                </td>

                                <td class="px-4 py-3 text-sm font-medium">
                                    <div class="flex space-x-2">

                                        <a href="{{ route('configuration.edit', $config->id) }}"
                                            class="text-green-600 hover:text-green-900" title="Editar">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <form action="{{ route('configuration.destroy', $config->id) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" title="Eliminar"
                                                onclick="return confirm('¿Estás seguro de eliminar esta configuración?')">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-4 text-center text-sm text-gray-500">No se encontraron
                                    configuraciones</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="mt-4">
                {{ $configurations->links() }}
            </div>
        </div>
    </div>
@endsection
