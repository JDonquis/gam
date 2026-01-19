<div class="flex flex-col">
    <header class="flex justify-between items-center h-16 py-4 px-6 bg-white">
        <!-- start::Mobile menu button -->
        <div class="flex items-center">
            <button @click="menuOpen = true"
                class="text-gray-500 hover:text-primary focus:outline-none lg:hidden transition duration-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7">
                    </path>
                </svg>
            </button>
        </div>
        <!-- end::Mobile menu button -->

        <!-- start::Right side top menu -->
        <div class="flex items-center">

            <!-- start::Notifications -->
            <div class="relative mx-6 group"> <!-- Agregada clase group aquí -->
                <!-- start::Main link -->
                <div class="cursor-pointer flex">
                    <svg class="w-6 h-6 cursor-pointer hover:text-primary" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                        </path>
                    </svg>
                    <sub>
                        @php
                            $todayNotifications = auth()
                                ->user()
                                ->notifications()
                                ->whereDate('created_at', today())
                                ->get();
                            $unreadCount = $todayNotifications->whereNull('read_at')->count();
                        @endphp
                        @if ($unreadCount > 0)
                            <span class="bg-red-600 text-gray-100 px-1.5 py-0.5 rounded-full -ml-1 animate-pulse">
                                {{ $unreadCount }}
                            </span>
                        @endif
                    </sub>
                </div>
                <!-- end::Main link -->

                <!-- start::Submenu -->
                <div
                    class="absolute right-0 w-96 top-11 border border-gray-300 z-10 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform translate-y-2 group-hover:translate-y-0">
                    <!-- Cambiado completamente -->
                    <!-- start::Submenu content -->
                    <div class="bg-white rounded max-h-96 overflow-y-scroll custom-scrollbar shadow-lg">
                        <!-- start::Submenu header -->
                        <div class="flex items-center justify-between px-4 py-2 bg-gray-50">
                            <span class="font-bold">Notificaciones de Hoy</span>
                            @if ($unreadCount > 0)
                                <span class="text-xs px-1.5 py-0.5 bg-red-600 text-white rounded">
                                    {{ $unreadCount }} nuevas
                                </span>
                            @endif
                        </div>
                        <hr>
                        <!-- end::Submenu header -->

                        @if ($todayNotifications->count() > 0)
                            @foreach ($todayNotifications as $notification)
                                @php
                                    $message = $notification->data['message'] ?? 'Sin mensaje';
                                    $url = $notification->data['url'] ?? '#';

                                    // Determinar icono basado en el mensaje
                                    $iconClass = 'bg-blue-100 text-blue-600';
                                    $iconPath =
                                        'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z';

                                    if (
                                        str_contains(strtolower($message), 'completamente') ||
                                        str_contains(strtolower($message), 'éxito')
                                    ) {
                                        $iconClass = 'bg-green-100 text-green-600';
                                        $iconPath = 'M5 13l4 4L19 7';
                                    } elseif (
                                        str_contains(strtolower($message), 'error') ||
                                        str_contains(strtolower($message), 'fallo')
                                    ) {
                                        $iconClass = 'bg-red-100 text-red-600';
                                        $iconPath = 'M6 18L18 6M6 6l12 12';
                                    } elseif (
                                        str_contains(strtolower($message), 'advertencia') ||
                                        str_contains(strtolower($message), 'warning')
                                    ) {
                                        $iconClass = 'bg-yellow-100 text-yellow-600';
                                        $iconPath =
                                            'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z';
                                    }
                                @endphp

                                <!-- start::Submenu link -->
                                <a href="{{ $url }}"
                                    class="flex items-center justify-between py-4 px-3 hover:bg-gray-50 {{ $notification->read_at ? '' : 'bg-blue-50' }} transition-colors duration-150"
                                    @if (!$notification->read_at) onclick="markNotificationAsRead('{{ $notification->id }}', event, this)" @endif>
                                    <div class="flex items-center">
                                        <!-- Icono según el tipo de mensaje -->
                                        <svg class="w-8 h-8 p-1.5 rounded-full {{ $iconClass }} flex-shrink-0"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="{{ $iconPath }}"></path>
                                        </svg>

                                        <div class="text-sm ml-3 max-w-xs">
                                            <p class="text-gray-800 font-medium leading-tight">
                                                {{ $message }}
                                            </p>
                                            @if (!$notification->read_at)
                                                <span class="text-xs text-blue-600 font-medium mt-1 inline-block">●
                                                    Nueva</span>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="text-xs text-gray-500 whitespace-nowrap ml-2 flex-shrink-0">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </span>
                                </a>
                                <!-- end::Submenu link -->

                                @if (!$loop->last)
                                    <hr class="my-1">
                                @endif
                            @endforeach
                        @else
                            <!-- start::Empty state -->
                            <div class="py-8 px-4 text-center">
                                <svg class="w-12 h-12 text-gray-400 mx-auto" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                                    </path>
                                </svg>
                                <p class="text-gray-600 mt-2">No hay notificaciones hoy</p>
                            </div>
                            <!-- end::Empty state -->
                        @endif

                        <!-- start::Mark all as read button -->
                        @if ($todayNotifications->count() > 0 && $unreadCount > 0)
                            <hr>
                            <div class="px-4 py-2 text-center bg-gray-50">
                                <form action="{{ route('notifications.markAllAsReadToday') }}" method="POST"
                                    id="markAllForm">
                                    @csrf
                                    <button type="submit"
                                        class="text-xs text-blue-600 hover:text-blue-800 font-medium px-3 py-1 rounded hover:bg-blue-50 transition-colors">
                                        Marcar todas como leídas
                                    </button>
                                </form>
                            </div>
                        @endif
                        <!-- end::Mark all as read button -->
                    </div>
                    <!-- end::Submenu content -->
                </div>
                <!-- end::Submenu -->
            </div>
            <!-- end::Notifications -->

            <!-- start::Profile -->
            <div class="relative group"> <!-- Agregada clase group aquí también -->
                <!-- start::Main link -->
                <div class="cursor-pointer">
                    <img src="{{ auth()->user()->photo ? asset('storage/' . auth()->user()->photo) : asset('assets/img/default.png') }}"
                        class="w-8 rounded-full" style="height: 32px; object-fit:cover;">
                </div>
                <!-- end::Main link -->

                <!-- start::Submenu -->
                <div
                    class="absolute right-0 w-40 top-11 border border-gray-300 z-20 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform translate-y-2 group-hover:translate-y-0 shadow-lg">
                    <!-- start::Submenu content -->
                    <div class="bg-white rounded">
                        <!-- start::Submenu link -->
                        <a href="{{ route('profile') }}"
                            class="flex items-center justify-between py-2 px-3 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <div class="text-sm ml-3">
                                    <p class="text-gray-600 font-bold capitalize">Mi perfil</p>
                                </div>
                            </div>
                        </a>
                        <!-- end::Submenu link -->

                        <hr>

                        <!-- start::Submenu link -->
                        <form method="GET" action="{{ route('logout') }}">
                            <button type="submit"
                                class="flex items-center justify-between w-full py-2 px-3 hover:bg-gray-50 text-left transition-colors">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                        </path>
                                    </svg>
                                    <span class="text-sm ml-3 text-gray-600 font-bold capitalize">Cerrar sesión</span>
                                </div>
                            </button>
                        </form>
                        <!-- end::Submenu link -->
                    </div>
                    <!-- end::Submenu content -->
                </div>
                <!-- end::Submenu -->
            </div>
            <!-- end::Profile -->
        </div>
        <!-- end::Right side top menu -->
    </header>
</div>

<script>
    // Función para marcar notificación como leída - CORREGIDO
    function markNotificationAsRead(notificationId, event, element) {
        // Prevenir la navegación inmediata
        event.preventDefault();

        // Usar la ruta named de Laravel
        const url = '{{ route('notifications.markAsRead', ['notification' => ':id']) }}'.replace(':id',
            notificationId);

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        }).then(response => {
            if (response.ok) {
                return response.json();
            }
            throw new Error('Error en la respuesta');
        }).then(data => {
            if (data.success) {
                // Ahora navegar al URL
                window.location.href = element.href;
            } else {
                // Si falla, navegar normalmente
                window.location.href = element.href;
            }
        }).catch(error => {
            console.error('Error:', error);
            // En caso de error, navegar normalmente
            window.location.href = element.href;
        });
    }

    // Manejar el formulario de marcar todas como leídas - CORREGIDO
    document.getElementById('markAllForm')?.addEventListener('submit', function(e) {
        e.preventDefault();

        fetch(this.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        }).then(response => {
            if (response.ok) {
                return response.json();
            }
            throw new Error('Error en la respuesta');
        }).then(data => {
            if (data.success) {
                window.location.reload();
            }
        }).catch(error => {
            console.error('Error:', error);
            window.location.reload();
        });
    });

    // Mantener el dropdown visible cuando se hace hover sobre él
    document.querySelectorAll('.group').forEach(group => {
        const dropdown = group.querySelector('[class*="opacity-0"]');
        if (dropdown) {
            dropdown.addEventListener('mouseenter', () => {
                dropdown.classList.add('opacity-100', 'visible', 'translate-y-0');
            });
            dropdown.addEventListener('mouseleave', () => {
                dropdown.classList.remove('opacity-100', 'visible', 'translate-y-0');
            });
        }
    });
</script>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 3px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }

    /* Transiciones suaves */
    .transition-all {
        transition: all 0.3s ease;
    }

    /* Asegurar que el dropdown esté por encima de otros elementos */
    .z-10 {
        z-index: 1000;
    }

    .z-20 {
        z-index: 1001;
    }
</style>
