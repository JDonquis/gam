@extends('layout.dashboard_layout')
@section('content')

<div class="h-full bg-gray-200 p-8">
    <div class="m-auto bg-white p-4 max-w-80 flex justify-center gap-8 rounded-lg shadow-xl py-8 mt-12">
        <div class="w-full">
            <h4 class="text-xl capitalize">Datos</h4>
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
                <form action="{{route('users.store')}}" method="POST" enctype="multipart/form-data" x-data="{
                    newPassword: '',
                    confirmPassword: '',
                    isPasswordFieldActive: false,
                    photoPreview: '{{ asset('assets/img/default.png') }}',
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
                            value="{{old('fullname')}}"
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
                            value="{{old('ci')}}"
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
                            value="{{old('phone_number')}}"
                        >
                    </div>



                    <div class="flex gap-2 mt-6">
                        <a href="{{route('users.index')}}" type="button" class="bg-secondary bg-opacity-20 hover:bg-opacity-40 rounded-lg px-6 py-1.5 text-secondary hover:shadow-xl transition duration-150">Cancelar</a>
                        <button type="submit" class="bg-primary hover:bg-primary-dark rounded-lg px-6 py-1.5 text-gray-100 hover:shadow-xl transition duration-150">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
