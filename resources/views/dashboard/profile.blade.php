@extends('layout.dashboard_layout')
@section('content')
 <!-- start:Page content -->
                <div class="h-full bg-gray-200 p-8">
                    <div class="bg-white rounded-lg shadow-xl pb-8">
                        <div class="w-full h-[250px]">
                            <img src="{{asset('assets/img/background.jpg')}}" class="w-full h-full rounded-tl-lg rounded-tr-lg object-cover">
                        </div>
                        <div class="flex flex-col items-center -mt-20">
                            <img src="{{ auth()->user()->photo ? asset('storage/' . auth()->user()->photo) : asset('assets/img/default.png') }}" class="w-40 border-4 border-white rounded-full">
                            <div class="flex items-center space-x-2 mt-2">
                                <p class="text-2xl">{{auth()->user()->fullname}}</p>
                                <span class="bg-blue-500 rounded-full p-1" title="Verified">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="text-gray-100 h-2.5 w-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7" />
                                    </svg>
                                </span>
                            </div>
                            <p class="text-sm text-gray-500">Secretaria de Salud Falcón</p>
                        </div>
                        <div class="flex-1 flex flex-col items-center lg:items-end justify-end px-8 mt-2">
                            <div class="flex items-center space-x-4 mt-2">
                                <a href="{{route('profile.edit')}}" class="flex items-center bg-blue-600 hover:bg-blue-700 text-gray-100 px-4 py-2 rounded text-sm space-x-2 transition duration-100">
                                    <svg class="h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.779 17.779 4.36 19.918 6.5 13.5m4.279 4.279 8.364-8.643a3.027 3.027 0 0 0-2.14-5.165 3.03 3.03 0 0 0-2.14.886L6.5 13.5m4.279 4.279L6.499 13.5m2.14 2.14 6.213-6.504M12.75 7.04 17 11.28"/>
                                    </svg>
                                    <span>Actualizar</span>
                                </a>
                                <form method="POST" action="{{ route('profile.destroy') }}"
                                    x-data="{ confirmDelete() { return confirm('¿Estás seguro de eliminar tu cuenta? Esta acción es irreversible.') } }"
                                    @submit.prevent="if(confirmDelete()) { $el.submit() }">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="flex items-center bg-blue-600 hover:bg-red-700 text-gray-100 px-4 py-2 rounded text-sm space-x-2 transition duration-100">
                                        <svg class="h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/>
                                        </svg>
                                        <span>Eliminar</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>



<div class="my-4 flex flex-col 2xl:flex-row gap-4">
    <!-- Columna 1: Personal Info -->
    <div class="w-full 2xl:w-1/2 bg-white rounded-lg shadow-xl p-8 h-fit">
        <h4 class="text-xl text-gray-900 font-bold">Información personal</h4>
        <ul class="mt-2 text-gray-700">
            <li class="flex border-y py-2 ">
                <span class="font-bold w-fit">Nombre y apellido:</span>
                <span class="text-gray-700 px-2">{{auth()->user()->fullname}}</span>
            </li>
            <li class="flex border-y py-2 ">
                <span class="font-bold w-fit">Cédula:</span>
                <span class="text-gray-700 px-2">{{auth()->user()->ci}}</span>
            </li>
            <li class="flex border-b py-2">
                <span class="font-bold w-fit">Registrado en:</span>
                <span class="text-gray-700 px-2">
                {{ auth()->user()->created_at->format('d M Y') }}
                ({{ auth()->user()->created_at->diffForHumans() }})
                </span>
            </li>
            <li class="flex border-b py-2">
                <span class="font-bold w-fit">Teléfono:</span>
                <span class="text-gray-700 px-2">{{ auth()->user()->phone_number }}</span>
            </li>
        </ul>
    </div>

    <!-- Columna 2: Activity Log -->
    <div class="w-full 2xl:w-1/2 bg-white rounded-lg shadow-xl p-8">
        <h4 class="text-xl text-gray-900 font-bold">Actividades recientes</h4>
        <div class="relative px-4">
            <div class="absolute h-full border border-dashed border-opacity-20 border-secondary"></div>

            <!-- Timeline item -->
            @if (count($activities) <= 0)
                <div class="flex items-center w-full my-6 -ml-1.5">
                <div class="w-1/12">
                    <div class="w-3.5 h-3.5 bg-primary rounded-full"></div>
                </div>
                <div class="w-11/12">
                    <p class="text-sm">Ninguna actividad realizada hasta ahora</p>
                </div>
            </div>
            @endif
            @foreach ( $activities as $activity )
                <div class="flex items-center w-full my-6 -ml-1.5">
                <div class="w-1/12">
                    <div class="w-3.5 h-3.5 bg-primary rounded-full"></div>
                </div>
                <div class="w-11/12">
                    <p class="text-sm">{{ $activity->typeActivity->name }}</p>
                    <p class="text-xs text-gray-500">{{ $activity->created_at->diffForHumans() }}</p>
                </div>
            </div>
            @endforeach


        </div>
    </div>
</div>

                </div>
@endsection

