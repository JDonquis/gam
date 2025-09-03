@extends('layout.dashboard_layout')

@section('content')
    <div class="h-full bg-gray-200 p-8">
        <div x-data="{ filter: false }" class="bg-white rounded-lg px-8 py-6">
            <div class="flex justify-between items-center mb-6">
                <h4 class="text-xl font-semibold">Renuncias</h4>
            </div>

            <!-- Búsqueda -->
            <div class="mt-8 mb-3 flex flex-col md:flex-row items-start md:items-center md:justify-between">
                <div class="flex items-center justify-center space-x-4">
                    <form class="relative flex items-center" method="GET" action="{{ url()->current() }}">
                        <input type="search" name="search" id="search"
                            class="flex-1 py-0.5 pl-10 border-gray-300 focus:outline-none focus:ring-0 focus:border-gray-300"
                            placeholder="Buscar..." value="{{ request('search') }}">
                        <button type="submit" class="absolute left-2 bg-transparent flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Tabla compacta -->
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-12">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Médico</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Motivo</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Carta de Renuncia
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Documento</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($resignations as $resignation)
                            <tr class="{{ $loop->odd ? 'bg-gray-50' : 'bg-white' }} hover:bg-gray-100">
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $resignation->id }}</td>

                                <td class="px-4 py-3 text-sm text-gray-500">
                                    @if ($resignation->doctor)
                                        <div class="flex items-center space-x-1">
                                            <a href="{{ route('doctors.show', $resignation->doctor->id) }}" target="_blank"
                                                class="text-yellow-600 hover:text-yellow-900 transition-colors duration-200 p-1 rounded hover:bg-yellow-50"
                                                title="Ver detalles del médico">
                                                <span class="truncate max-w-xs">{{ $resignation->doctor->ci }}</span>
                                            </a>
                                        </div>
                                    @else
                                        <span class="text-gray-400">No aplica</span>
                                    @endif
                                </td>

                                <td class="px-4 py-3">
                                    <div class="text-sm font-medium text-gray-900 truncate"
                                        title="{{ $resignation->reason }}">
                                        {{ Str::limit($resignation->reason, 50) }}
                                    </div>
                                </td>

                                <td class="px-4 py-3 text-sm text-gray-500">
                                    @if ($resignation->resignation_letter)
                                        <a href="{{ asset('storage/' . $resignation->resignation_letter) }}" target="_blank"
                                            class="text-blue-600 hover:text-blue-800 underline">
                                            Ver carta
                                        </a>
                                    @else
                                        <span class="text-gray-400">No disponible</span>
                                    @endif
                                </td>

                                <td class="px-4 py-3 text-sm text-gray-500">
                                    @if ($resignation->document)
                                        <a href="{{ asset('storage/' . $resignation->document) }}" target="_blank"
                                            class="text-blue-600 hover:text-blue-800 underline">
                                            Ver documento
                                        </a>
                                    @else
                                        <span class="text-gray-400">No disponible</span>
                                    @endif
                                </td>

                                <td class="px-4 py-3 text-sm text-gray-500"
                                    title="{{ $resignation->created_at->format('d/m/Y H:i') }}">
                                    {{ $resignation->created_at->format('M j, Y') }}
                                </td>

                                <td class="px-4 py-3 text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('resignations.edit', $resignation->id) }}"
                                            class="text-green-600 hover:text-green-900" title="Ver">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>

                                        <form action="{{ route('resignations.destroy', $resignation->id) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" title="Eliminar">
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
                                <td colspan="7" class="px-4 py-4 text-center text-sm text-gray-500">No se encontraron
                                    renuncias registradas</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="mt-4">
                {{ $resignations->links() }}
            </div>
        </div>
    </div>
@endsection
