@extends('layout.dashboard_layout')

@section('content')
    <div class="h-full bg-gray-200 p-8">
        <div x-data="{ filter: false }" class="bg-white rounded-lg px-8 py-6">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <h4 class="text-xl font-semibold">Documentos Registrados</h4>
                @if (!$totalCensus == 0)
                    <a href="{{ route('census.create') }}"
                        class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-lg flex items-center space-x-2 text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z"
                                clip-rule="evenodd" />
                        </svg>
                        <span>Registrar Documento</span>
                    </a>
                @endif
            </div>

            @if ($totalCensus == 0)
                <!-- Estado vacío -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden p-12 text-center">
                    <div class="mx-auto max-w-md">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M7 21h10a2 2 0 01-2-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                            </path>
                        </svg>
                        <h3 class="mt-2 text-lg font-medium text-gray-900">No hay documentos registrados</h3>
                        <p class="mt-1 text-sm text-gray-500">Comienza registrando tu primer documento.</p>
                        <div class="mt-6">
                            <a href="{{ route('census.create') }}"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Registrar documento
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <!-- Filtros y búsqueda -->
                <div class="mt-8 mb-3 flex flex-col md:flex-row items-start md:items-center md:justify-between">
                    <div class="flex items-center justify-center space-x-4">
                        <form class="relative flex items-center" method="GET" action="{{ url()->current() }}">
                            <input type="search" name="search" id="search"
                                class="flex-1 py-0.5 pl-10 border-gray-300 focus:outline-none focus:ring-0 focus:border-gray-300"
                                placeholder="Buscar censos..." value="{{ request('search') }}">
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
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 my-4">
                                <input type="text" name="title"
                                    class="flex-1 py-1 border-gray-300 rounded focus:border-gray-300 focus:outline-none focus:ring-0"
                                    placeholder="Nombre" value="{{ request('title') }}">

                                <!-- Nuevo filtro: Tipo de documento -->
                                <select name="type"
                                    class="flex-1 py-1 border-gray-300 rounded focus:border-gray-300 focus:outline-none focus:ring-0">
                                    <option value="">Todos los tipos</option>
                                    <option value="1" {{ request('type') == '1' ? 'selected' : '' }}>Censo de
                                        residentes</option>
                                    <option value="2" {{ request('type') == '2' ? 'selected' : '' }}>Renuncias</option>
                                </select>

                                <div class="flex space-x-2">
                                    <input type="date" name="start_date"
                                        class="flex-1 py-1 border-gray-300 rounded focus:border-gray-300 focus:outline-none focus:ring-0"
                                        placeholder="Desde" value="{{ request('start_date') }}">
                                    <input type="date" name="end_date"
                                        class="flex-1 py-1 border-gray-300 rounded focus:border-gray-300 focus:outline-none focus:ring-0"
                                        placeholder="Hasta" value="{{ request('end_date') }}">
                                </div>

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
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Progreso</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tamaño</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Registrado por
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($censuses as $census)
                                <tr class="{{ $loop->odd ? 'bg-gray-50' : 'bg-white' }} hover:bg-gray-100"
                                    id="census-row-{{ $census->id }}">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8">
                                                <svg class="h-8 w-8 text-blue-500" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="1.5"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                    </path>
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900 truncate"
                                                    title="{{ $census->title }}">
                                                    {{ $census->title }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-4 py-3">
                                        <div class="flex items-center">
                                            <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                                <div class="bg-blue-600 h-2 rounded-full"
                                                    style="width: {{ $census->percentage }}%"
                                                    id="progress-bar-{{ $census->id }}"></div>
                                            </div>
                                            <span class="text-xs font-medium text-gray-700"
                                                id="percentage-text-{{ $census->id }}">{{ $census->percentage }}%</span>
                                            @if (!$census->is_completed)
                                                <span class="ml-2 text-xs text-blue-600 animate-pulse"
                                                    id="status-text-{{ $census->id }}">Procesando...</span>
                                            @else
                                                <span class="ml-2 text-xs text-green-600"
                                                    id="status-text-{{ $census->id }}">Completado</span>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="px-4 py-3 text-sm text-gray-500">
                                        @if ($census->type_document_id == 1)
                                            <span
                                                class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">Censo
                                                de residentes</span>
                                        @elseif($census->type_document_id == 2)
                                            <span
                                                class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">Renuncias</span>
                                        @else
                                            <span
                                                class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded">Desconocido</span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-3 text-sm text-gray-500">{{ $census->size }} MB</td>

                                    <td class="px-4 py-3 text-sm text-gray-500">{{ $census->user->fullname }}</td>

                                    <td class="px-4 py-3 text-sm text-gray-500"
                                        title="{{ $census->created_at->format('d/m/Y H:i') }}">
                                        {{ $census->created_at->format('M j, Y') }}
                                    </td>

                                    <td class="px-4 py-3 text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('census.show', $census->id) }}"
                                                class="text-blue-600 hover:text-blue-900" title="Ver">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                    </path>
                                                </svg>
                                            </a>
                                            <a href="{{ route('census.download', $census->id) }}"
                                                class="text-green-600 hover:text-green-900" title="Descargar">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4">
                                                    </path>
                                                </svg>
                                            </a>
                                            <form action="{{ route('census.destroy', $census->id) }}" method="POST"
                                                class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900"
                                                    title="Eliminar"
                                                    onclick="return confirm('¿Estás seguro de eliminar este documento?. Esto eliminara todas las incidencias relacionadas con este documento')">
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-4 text-center text-sm text-gray-500">No se
                                        encontraron
                                        documentos registrados</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                @if ($censuses->hasPages())
                    <div class="mt-4">
                        {{ $censuses->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>

    @section('custom-script')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Obtener todos los IDs de censos que no están completados
                const incompleteCensuses = @json($censuses->where('is_completed', false)->pluck('id'));

                if (incompleteCensuses.length > 0) {
                    // Iniciar el intervalo para actualizar el progreso cada 3 segundos
                    const interval = setInterval(updateProgress, 3000);

                    function updateProgress() {
                        fetch('{{ route('census.progress') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    census_ids: incompleteCensuses
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                // Actualizar cada censo con los nuevos datos
                                data.forEach(census => {
                                    const progressBar = document.getElementById(
                                        `progress-bar-${census.id}`);
                                    const percentageText = document.getElementById(
                                        `percentage-text-${census.id}`);
                                    const statusText = document.getElementById(`status-text-${census.id}`);

                                    if (progressBar && percentageText && statusText) {
                                        progressBar.style.width = `${census.percentage}%`;
                                        percentageText.textContent = `${census.percentage}%`;

                                        if (census.is_completed) {
                                            statusText.textContent = 'Completado';
                                            statusText.classList.remove('text-blue-600', 'animate-pulse');
                                            statusText.classList.add('text-green-600');

                                            // Eliminar este ID de la lista de incompletos
                                            const index = incompleteCensuses.indexOf(census.id);
                                            if (index !== -1) {
                                                incompleteCensuses.splice(index, 1);
                                            }
                                        }
                                    }
                                });

                                // Si no hay más censos incompletos, detener el intervalo
                                if (incompleteCensuses.length === 0) {
                                    clearInterval(interval);
                                }
                            })
                            .catch(error => console.error('Error:', error));
                    }

                    // Ejecutar inmediatamente al cargar la página
                    updateProgress();
                }
            });
        </script>
    @endsection
@endsection
