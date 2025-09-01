@extends('layout.dashboard_layout')

@section('content')
    <div class="h-full bg-gray-200 p-8">
        <!-- start::Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-10">
            <div class="px-6 py-6 bg-white rounded-lg shadow-xl">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-sm text-sky-600">Medicos registrados</span>
                    <span
                        class="text-xs bg-gray-200 hover:bg-gray-500 text-gray-500 hover:text-gray-200 px-2 py-1 rounded-lg transition duration-200 cursor-default">Hoy</span>
                </div>
                <div class="flex items-center justify-between mt-6">
                    <div>
                        <svg class="w-12 2xl:w-16 h-12 2xl:h-16 p-1 2xl:p-3 bg-sky-400 bg-opacity-20 rounded-full text-sky-600 border border-sky-600"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                            <g id="SVGRepo_iconCarrier">
                                <path
                                    d="M16,1H8A5.006,5.006,0,0,0,3,6v8a9,9,0,0,0,18,0V6A5.006,5.006,0,0,0,16,1ZM5,6A3,3,0,0,1,8,3h8a3,3,0,0,1,3,3v5H5Zm14,8A7,7,0,0,1,5,14V13H19ZM13,6h2V8H13v2H11V8H9V6h2V4h2Z">
                                </path>
                            </g>
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <div class="flex items-end">
                            <span class="text-2xl 2xl:text-4xl font-bold">{{ $doctorsCount }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-6 py-6 bg-white rounded-lg shadow-xl">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-sm text-green-600">Medicos cursando</span>
                    <span
                        class="text-xs bg-gray-200 hover:bg-gray-500 text-gray-500 hover:text-gray-200 px-2 py-1 rounded-lg transition duration-200 cursor-default">Hoy</span>
                </div>
                <div class="flex items-center justify-between mt-6">
                    <div>
                        <svg class="w-12 2xl:w-16 h-12 2xl:h-16 p-1 2xl:p-3 bg-green-400 bg-opacity-20 rounded-full text-green-600 border border-green-600"
                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3.78552 9.5 12.7855 14l9-4.5-9-4.5-8.99998 4.5Zm0 0V17m3-6v6.2222c0 .3483 2 1.7778 5.99998 1.7778 4 0 6-1.3738 6-1.7778V11" />
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <div class="flex items-end">
                            <span class="text-2xl 2xl:text-4xl font-bold">{{ $doctorsInCourse }}</span>

                        </div>
                    </div>
                </div>
            </div>
            <div class="px-6 py-6 bg-white rounded-lg shadow-xl">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-sm text-yellow-600">Medicos con incidencias</span>
                    <span
                        class="text-xs bg-gray-200 hover:bg-gray-500 text-gray-500 hover:text-gray-200 px-2 py-1 rounded-lg transition duration-200 cursor-default">Hoy</span>
                </div>
                <div class="flex items-center justify-between mt-6">
                    <div>

                        <svg class="w-12 2xl:w-16 h-12 2xl:h-16 p-1 2xl:p-3 bg-yellow-400 bg-opacity-20 rounded-full text-yellow-600 border border-yellow-600"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                            </path>
                        </svg>

                    </div>
                    <div class="flex flex-col">
                        <div class="flex items-end">
                            <span class="text-2xl 2xl:text-4xl font-bold">{{ $doctorsWithIncidence }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-6 py-6 bg-white rounded-lg shadow-xl">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-sm text-red-600">Medicos con sanciones</span>
                    <span
                        class="text-xs bg-gray-200 hover:bg-gray-500 text-gray-500 hover:text-gray-200 px-2 py-1 rounded-lg transition duration-200 cursor-default">Hoy</span>
                </div>
                <div class="flex items-center justify-between mt-6">
                    <div>
                        <svg class="w-12 2xl:w-16 h-12 2xl:h-16 p-1 2xl:p-3 bg-red-400 bg-opacity-20 rounded-full text-red-600 border border-red-600"
                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                                d="m6 6 12 12m3-6a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <div class="flex items-end">
                            <span class="text-2xl 2xl:text-4xl font-bold">{{ $doctorsWithSanction }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end::Stats -->

        <div class="py-5 grid grid-cols-6 md:grid-cols-6 xl:grid-cols-6 gap-10">

            <!-- start::Table -->
            <div class="bg-white rounded-lg px-8 py-6 overflow-x-hidden custom-scrollbar col-span-6 md:col-span-4">
                <h4 class="text-xl font-semibold p-3">Actividades recientes</h4>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-12">ID</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actividad</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usuario</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-20">Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($activities as $activity)
                                <tr class="{{ $loop->odd ? 'bg-gray-50' : 'bg-white' }} hover:bg-gray-100">
                                    <td class="px-4 py-3 text-sm text-gray-500">{{ $activity->id }}</td>

                                    <td class="px-4 py-3">
                                        <div class="text-sm font-medium text-gray-900 capitalize">
                                            {{ $activity->typeActivity->name }}
                                        </div>
                                    </td>

                                    <td class="px-4 py-3 text-sm text-gray-500">
                                        {{ $activity->user->fullname ?? $activity->data['fullname'] }}
                                    </td>

                                    <td class="px-4 py-3 text-sm text-gray-500"
                                        title="{{ $activity->created_at->format('d/m/Y H:i') }}">
                                        {{ $activity->created_at->format('M j, Y') }}
                                    </td>

                                    <td class="px-4 py-3 text-sm">
                                        <div class="flex justify-center">
                                            <a href="{{ $activity->data['url'] ?? '#' }}"
                                                class="text-blue-600 hover:text-blue-900" title="Ver detalles">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
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
                                    <td colspan="5" class="px-4 py-4 text-center text-sm text-gray-500">No se
                                        encontraron actividades registradas</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
            <!-- end::Table -->



            <!-- start::Total stats -->
            <div class="col-span-6 md:col-span-2 p-6 space-y-6 bg-white shadow-xl rounded-lg">
                <h4 class="text-xl font-semibold mb-4 capitalize">Estadisticas historicas</h4>
                <div class="grid grid-cols-2 gap-4 h-40">
                    <div
                        class="bg-green-300 bg-opacity-20 text-green-700 flex flex-col items-center justify-center rounded-lg">
                        <span class="text-4xl font-bold">{{ $censusesCount }}</span>
                        <span>Censos</span>
                    </div>
                    <div
                        class="bg-indigo-300 bg-opacity-20 text-indigo-700 flex flex-col items-center justify-center rounded-lg">
                        <span class="text-4xl font-bold">{{ $usersCount }}</span>
                        <span>Usuarios</span>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-2 h-32">
                    <div
                        class="bg-yellow-300 bg-opacity-20 text-yellow-700 flex flex-col items-center justify-center rounded-lg">
                        <span class="text-3xl font-bold">{{ $incidencesCount }}</span>
                        <span>Incidencias</span>
                    </div>
                    <div
                        class="bg-blue-300 bg-opacity-20 text-blue-700 flex flex-col items-center justify-center rounded-lg">
                        <span class="text-3xl font-bold">24</span>
                        <span>Renuncias</span>
                    </div>
                    <div
                        class="bg-red-300 bg-opacity-20 text-red-700 flex flex-col items-center justify-center rounded-lg">
                        <span class="text-3xl font-bold">37</span>
                        <span>Sanciones</span>
                    </div>
                </div>
            </div>
            <!-- end::Total stats -->
        </div>
        <!-- end::Stats -->
    </div>




    </div>
@endsection
