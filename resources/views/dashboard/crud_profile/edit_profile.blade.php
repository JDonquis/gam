@extends('layout.dashboard_layout')
@section('content')

<div class="h-full bg-gray-200 p-8">
    <div class="m-auto bg-white p-4 max-w-80 flex justify-center gap-8 rounded-lg shadow-xl py-8 mt-12">
        <div class="w-full">
            <h4 class="text-xl capitalize">Mis datos</h4>
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
                <form action="{{route('profile.update')}}" method="POST" enctype="multipart/form-data" x-data="{
                    newPassword: '',
                    confirmPassword: '',
                    isPasswordFieldActive: false,
                    photoPreview: '{{ $data->photo ? asset('storage/'.$data->photo) : asset('assets/img/default.png') }}',
                    updatePhotoPreview(event) {
                        const input = event.target;
                        if (input.files && input.files[0]) {
                            const reader = new FileReader();
                            reader.onload = (e) => {
                                this.photoPreview = e.target.result;
                            };
                            reader.readAsDataURL(input.files[0]);
                        }
                    },
                    init() {
                        this.$watch('newPassword', (value) => {
                            this.isPasswordFieldActive = value.length > 0;
                        });
                    }
                }">
                    @csrf
                    @method('PUT')

                    <!-- Campo de foto de perfil -->
                    <div class="flex flex-col items-center mb-6">
                        <div class="relative group w-32 h-32 ">
                            <img :src="photoPreview"
                                 class="w-full h-full rounded-full object-cover border-2 border-gray-300 shadow-md group-hover:border-primary transition duration-300">
                            <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition duration-300 bg-black bg-opacity-30 rounded-full cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <input type="file"
                                   name="photo"
                                   id="photo"
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                   accept=".png,.jpeg,.jpg,image/png,image/jpeg"
                                   @change="updatePhotoPreview">
                        </div>
                        <label for="photo" class="mt-2 text-sm text-gray-600 cursor-pointer hover:text-primary transition duration-200">
                            Cambiar foto de perfil
                        </label>
                    </div>

                    <div class="flex flex-col my-4">
                        <label for="fullname" class="text-gray-700">Nombres y apellidos</label>
                        <input
                            type="text"
                            name="fullname"
                            id="fullname"
                            class="flex-1 py-1 border-gray-300 mt-1 rounded focus:border-gray-300 focus:outline-none focus:ring-0"
                            placeholder="Jhon Doe"
                            value="{{old('fullname',$data->fullname)}}"
                            required
                        >
                    </div>

                    <div class="flex flex-col my-4">
                        <label for="ci" class="text-gray-700">Cédula</label>
                        <input
                            type="text"
                            name="ci"
                            id="ci"
                            class="flex-1 py-1 border-gray-300 mt-1 rounded focus:border-gray-300 focus:outline-none focus:ring-0"
                            placeholder="12345678"
                            value="{{old('ci',$data->ci)}}"
                            required
                        >
                    </div>

                    <div class="flex flex-col my-4">
                        <label for="phone_number" class="text-gray-700">Teléfono</label>
                        <input
                            type="text"
                            name="phone_number"
                            id="phone_number"
                            class="flex-1 py-1 border-gray-300 mt-1 rounded focus:outline-none focus:ring-0 focus:border-gray-300"
                            placeholder="+5812345678"
                            value="{{old('phone_number',$data->phone_number)}}"
                        >
                    </div>

                    <div class="flex flex-col my-4">
                        <label for="new_password" class="text-gray-700">Nueva contraseña</label>
                        <div
                            x-data="{ show: false }"
                            class="relative flex-1 mt-1 flex items-center"
                        >
                            <input
                                x-model="newPassword"
                                :type="show ? 'text': 'password'"
                                name="new_password"
                                id="new_password"
                                class="flex-1 py-1 pr-10 border-gray-300 rounded focus:outline-none focus:ring-0 focus:border-gray-300"
                                placeholder="Tu nueva contraseña"
                            >
                            <button type="button" class="absolute right-2 bg-transparent flex items-center justify-center" @click="show = !show">
                                <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                </svg>
                                <svg x-show="show" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-col my-4">
                        <label for="confirm_password" class="text-gray-700">Confirmar contraseña</label>
                        <div
                            x-data="{ show: false }"
                            class="relative flex-1 mt-1 flex items-center"
                        >
                            <input
                                x-model="confirmPassword"
                                :type="show ? 'text': 'password'"
                                name="confirm_password"
                                id="confirm_password"
                                :disabled="!isPasswordFieldActive"
                                :required="isPasswordFieldActive"
                                :class="{
                                    'flex-1 py-1 pr-10 border-gray-300 rounded focus:outline-none focus:ring-0 focus:border-gray-300': isPasswordFieldActive,
                                    'flex-1 py-1 pr-10 border-gray-300 rounded bg-gray-100 text-gray-400 cursor-not-allowed': !isPasswordFieldActive
                                }"
                                placeholder="Confirma tu nueva contraseña"
                            >
                            <button
                                type="button"
                                class="absolute right-2 bg-transparent flex items-center justify-center"
                                @click="show = !show"
                                :class="{'cursor-not-allowed text-gray-400': !isPasswordFieldActive}"
                                :disabled="!isPasswordFieldActive"
                            >
                                <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                </svg>
                                <svg x-show="show" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex gap-2 mt-6">
                        <a href="{{route('profile')}}" type="button" class="bg-secondary bg-opacity-20 hover:bg-opacity-40 rounded-lg px-6 py-1.5 text-secondary hover:shadow-xl transition duration-150">Cancelar</a>
                        <button type="submit" class="bg-primary hover:bg-primary-dark rounded-lg px-6 py-1.5 text-gray-100 hover:shadow-xl transition duration-150">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
