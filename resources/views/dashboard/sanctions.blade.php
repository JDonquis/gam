@extends('layout.dashboard_layout')

@section('content')
    <div class="h-full bg-gray-200 p-8">
        <!-- start::Advance Table Filters -->
        <div class="bg-white rounded-lg px-8 py-6 overflow-x-hidden custom-scrollbar">
            <div class="flex justify-between items-center">
                <h4 class="text-xl font-semibold">Sanciones</h4>
                <!-- No hay botón de crear como solicitaste -->
            </div>

            <!-- Tabla de sanciones -->
            <div class="border border-gray-200 rounded-lg overflow-hidden mt-6">
                <div class="overflow-x-auto">
                    <table class="w-full table-fixed">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-16">ID</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-1/6">Cédula del
                                    Médico</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-1/6">Fecha de
                                    Inicio</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-1/6">Fecha de
                                    Fin</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-2/6">Razón</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-28">Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($sanctions as $sanction)
                                <tr class="{{ $loop->odd ? 'bg-gray-50' : 'bg-white' }} hover:bg-gray-100">
                                    <td class="px-4 py-3 text-sm text-gray-500">{{ $sanction->id }}</td>

                                    <td class="px-4 py-3 text-sm text-gray-500">
                                        @if ($sanction->doctor)
                                            <div class="flex items-center truncate">
                                                <svg class="h-4 w-4 mr-2 text-blue-500 flex-shrink-0" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                <span class="truncate">{{ $sanction->doctor->ci ?? 'N/A' }}</span>
                                            </div>
                                        @else
                                            <span class="text-gray-400 italic">Médico no encontrado</span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        @if ($sanction->start_date)
                                            {{ \Carbon\Carbon::parse($sanction->start_date)->format('d/m/Y') }}
                                        @else
                                            <span class="text-gray-400 italic">No especificada</span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        @if ($sanction->end_date)
                                            {{ \Carbon\Carbon::parse($sanction->end_date)->format('d/m/Y') }}
                                        @else
                                            <span class="text-gray-400 italic">No especificada</span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        <div class="truncate" title="{{ $sanction->reason }}">
                                            {{ $sanction->reason ?? 'Sin especificar' }}
                                        </div>
                                    </td>

                                    <td class="px-4 py-3 text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('sanctions.edit', $sanction->id) }}"
                                                class="text-green-600 hover:text-green-900 p-1 rounded-full hover:bg-green-100"
                                                title="Editar">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                            <form action="{{ route('sanctions.destroy', $sanction->id) }}" method="POST"
                                                class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-red-600 hover:text-red-900 p-1 rounded-full hover:bg-red-100"
                                                    title="Eliminar"
                                                    onclick="return confirm('¿Estás seguro de eliminar esta sanción?')">
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
                                    <td colspan="6" class="px-4 py-4 text-center text-sm text-gray-500">
                                        No se encontraron sanciones registradas
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Paginación -->
            <div class="mt-4">
                {{ $sanctions->links() }}
            </div>
        </div>
        <!-- end::Advance Table Filters -->
    </div>
@endsection
