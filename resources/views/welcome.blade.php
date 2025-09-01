<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GAM</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    @vite(['resources/css/app.css', 'resources/js/app.js'])


</head>

<body>
    <div x-data="{
        showPassword: false,
        showPasswordRecovery: false,
        recoveryForm: {
            ci: '',
            master_password: ''
        },
        openRecoveryModal() {
            this.showPasswordRecovery = true;
    
        },
        closeRecoveryModal() {
            this.showPasswordRecovery = false;
            this.recoveryForm = { ci: '', master_password: '' };
        }
    }" class="w-full min-h-screen flex items-center justify-center bg-gray-200">

        <div class="w-full h-screen flex items-center justify-center">
            <div class="w-full sm:w-5/6 md:w-2/3 lg:w-1/2 xl:w-1/3 2xl:w-1/4 h-full flex items-center justify-center">
                <div class="w-full px-12">
                    <!-- Logo y título -->


                    <h2 class="text-center text-2xl font-bold tracking-wide text-gray-800">INICIAR SESIÓN</h2>

                    @if ($errors->any())
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form class="my-8 text-sm" action="{{ route('login.post') }}" method="POST">
                        @csrf
                        <div class="flex flex-col my-4">
                            <label for="ci" class="text-gray-700">Cédula</label>
                            <input type="text" name="ci" id="ci" value="{{ old('ci') }}"
                                class="mt-2 p-2 border border-gray-300 focus:outline-none focus:ring-0 focus:border-gray-300 rounded text-sm text-gray-900"
                                placeholder="Ingrese su cédula" required>
                            @error('ci')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="flex flex-col my-4">
                            <label for="password" class="text-gray-700">Contraseña</label>
                            <div x-data="{ show: false }" class="relative flex items-center mt-2">
                                <input :type="show ? 'text' : 'password'" name="password" id="password"
                                    class="flex-1 p-2 pr-10 border border-gray-300 focus:outline-none focus:ring-0 focus:border-gray-300 rounded text-sm text-gray-900"
                                    placeholder="Ingrese su contraseña" required>
                                <button @click="show = !show" type="button"
                                    class="absolute right-2 bg-transparent flex items-center justify-center text-gray-700">
                                    <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21">
                                        </path>
                                    </svg>
                                    <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="display: none;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="my-4 flex items-center justify-center">
                            <button
                                class="bg-primary hover:bg-primary-dark rounded-lg w-full px-8 py-2.5 text-gray-100 hover:shadow-xl transition duration-150 uppercase font-medium">
                                INGRESAR
                            </button>
                        </div>

                        <div class="flex items-center justify-between mt-6">
                            <div class="w-full h-[1px] bg-gray-300"></div>
                            <span class="text-sm uppercase mx-4 text-gray-400">O</span>
                            <div class="w-full h-[1px] bg-gray-300"></div>
                        </div>

                        <div class="my-4 flex items-center justify-center">

                            <a href="#" @click="openRecoveryModal"
                                class="text-sm text-gray-600 hover:text-primary hover:underline cursor-pointer">
                                ¿Olvidó su contraseña?
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="hidden lg:flex lg:w-1/2 xl:w-2/3 2xl:w-3/4 h-full bg-cover"
                style="background-image: url('{{ asset('assets/img/doctors.jpg') }}');">
                <div class="w-full h-full flex flex-col items-center justify-center bg-black bg-opacity-30">
                    <div class="flex items-center justify-center space-x-4">
                        <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" class="w-24">
                        <h1 class="text-4xl xl:text-5xl 2xl:text-6xl font-bold text-white tracking-wider">GAM</h1>
                    </div>
                    <p class="text-gray-100 mt-6 px-16 text-center text-lg">Sistema de Gestión de Médicos</p>
                </div>
            </div>
        </div>

        <!-- Modal de recuperación de contraseña -->

        <div x-show="showPasswordRecovery" x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;"
            @click="
            if ($event.target === $el && showPasswordRecovery) {
                closeRecoveryModal();
            }
        ">

            <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-800">Recuperar contraseña</h3>
                        <button @click="showPasswordRecovery = false; recoveryForm = { ci: '', master_password: '' }"
                            class="text-gray-500 hover:text-gray-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form action="{{ route('password.recover') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="recovery_ci" class="block text-sm font-medium text-gray-700">Cédula</label>
                            <input type="text" id="recovery_ci" x-model="recoveryForm.ci" name="recover_ci"
                                required value="{{ old('recover_ci') }}"
                                class="mt-1 block w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-0 focus:border-gray-300">

                        </div>

                        <div class="mb-4">
                            <label for="master_password" class="block text-sm font-medium text-gray-700">Contraseña
                                Maestra</label>
                            <input type="password" id="master_password" x-model="recoveryForm.master_password"
                                name="master_password" required
                                class="mt-1 block w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-0 focus:border-gray-300">
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <button @click="closeRecoveryModal()" class="text-gray-500 hover:text-gray-700">
                                Cancelar
                            </button>
                            <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-primary rounded-md hover:bg-primary-dark transition duration-150">
                                Recuperar contraseña
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (window.toastr) {
                    window.toastr.success("{{ session('success') }}");
                } else {
                    alert("{{ session('success') }}");
                }
            });
        </script>
    @endif
</body>

</html>
