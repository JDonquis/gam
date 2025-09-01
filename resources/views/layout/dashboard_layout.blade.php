<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>GAM</title>

    <!-- Styles -->
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.5.1/chart.min.js"></script>
</head>

<body>
    <div x-data="{ menuOpen: false }" class="flex min-h-screen custom-scrollbar">
        <!-- start::Black overlay -->
        <div :class="menuOpen ? 'block' : 'hidden'" @click="menuOpen = false"
            class="fixed z-20 inset-0 bg-black opacity-50 transition-opacity lg:hidden"></div>
        <!-- end::Black overlay -->

        @include('layout.sidebar')

        <div class="lg:pl-64 w-full flex flex-col">
            <!-- start::Topbar -->
            @include('layout.header_layout')
            <!-- end::Topbar -->

            <!-- start:Page content -->
            @yield('content')
            <!-- end:Page content -->
        </div>
    </div>

    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                toastr.success("{{ session('success') }}");
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                toastr.error("{{ session('error') }}");
            });
        </script>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mostrar mensaje de sessionStorage si existe
            const flashMessage = sessionStorage.getItem('flashMessage');
            if (flashMessage) {
                toastr.success(flashMessage);
                sessionStorage.removeItem('flashMessage'); // Limpiar después de mostrar
            }

            // También puedes mantener el soporte para mensajes de sesión tradicionales
            @if (session('success'))
                toastr.success("{{ session('success') }}");
            @endif

            @if (session('error'))
                toastr.error("{{ session('error') }}");
            @endif
        });
    </script>

    @yield('custom-script')
</body>

</html>
