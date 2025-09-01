@extends('layout.dashboard_layout')

@section('content')
    <div class="h-full bg-gray-200 p-8">
        <div x-data="{ filter: false }" class="bg-white rounded-lg px-8 py-6">
            <div class="flex justify-between items-center mb-6">
                <h4 class="text-xl font-semibold">Usuarios</h4>
                <a href="{{ route('users.create') }}"
                    class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-lg flex items-center space-x-2 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z"
                            clip-rule="evenodd" />
                    </svg>
                    <span>Crear Usuario</span>
                </a>
            </div>

            <!-- Filtros y búsqueda -->
            <div class="mt-8 mb-3 flex flex-col md:flex-row items-start md:items-center md:justify-between">
                <div class="flex items-center justify-center space-x-4">
                    <form class="relative flex items-center" method="GET" action="{{ url()->current() }}">
                        <input type="search" name="search" id="search"
                            class="flex-1 py-0.5 pl-10 border-gray-300 focus:outline-none focus:ring-0 focus:border-gray-300"
                            placeholder="Buscar por nombre o cédula..." value="{{ request('search') }}">
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
                            <input type="text" name="fullname"
                                class="flex-1 py-1 border-gray-300 rounded focus:border-gray-300 focus:outline-none focus:ring-0"
                                placeholder="Nombre completo" value="{{ request('fullname') }}">
                            <input type="text" name="ci"
                                class="flex-1 py-1 border-gray-300 rounded focus:border-gray-300 focus:outline-none focus:ring-0"
                                placeholder="Cédula" value="{{ request('ci') }}">
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
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-48">Nombre</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-24">Cédula</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-24">Teléfono</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-32">Registro</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-28">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($users as $user)
                            <tr class="{{ $loop->odd ? 'bg-gray-50' : 'bg-white' }} hover:bg-gray-100">
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $user->id }}</td>

                                <td class="px-4 py-3">
                                    <div class="text-sm font-medium text-gray-900 truncate" title="{{ $user->fullname }}">
                                        {{ $user->fullname }}
                                    </div>
                                </td>

                                <td class="px-4 py-3 text-sm text-gray-500">{{ $user->ci }}</td>

                                <td class="px-4 py-3 text-sm text-gray-500 truncate">{{ $user->phone_number ?? 'N/A' }}
                                </td>

                                <td class="px-4 py-3 text-sm text-gray-500"
                                    title="{{ $user->created_at->format('d/m/Y H:i') }}">
                                    {{ $user->created_at->format('M j, Y') }}
                                </td>

                                <td class="px-4 py-3 text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('users.show', $user->id) }}"
                                            class="text-blue-600 hover:text-blue-900" title="Ver">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-4 text-center text-sm text-gray-500">No se encontraron
                                    usuarios registrados</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>
@endsection
