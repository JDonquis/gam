{{-- resources/views/errors/404.blade.php --}}
@extends('errors::minimal')

@section('title', __('Página no encontrada'))
@section('code', '404')
@section('message', __('La página solicitada no existe'))

@section('content')
    <style>
        .error-container {
            text-align: center;
            padding: 40px 20px;
        }

        .back-button {
            margin-top: 30px;
        }

        .btn-dashboard {
            display: inline-flex;
            align-items: center;
            padding: 12px 24px;
            background-color: #1f4b8e;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            transition: background-color 0.3s;
            margin-bottom: 30px;
            font-size: 12pt;

        }

        .btn-dashboard:hover {
            background-color: #112a52;
        }

        .btn-back {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 12px 24px;
            background-color: #6b7280;
            min-height: 48px;
            min-width: 190px;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            transition: background-color 0.3s;
            font-size: 12pt;

        }

        .btn-back:hover {
            background-color: #4b5563;
        }
    </style>

    <div class="error-container">

        <div class="back-button">
            @auth
                <a href="{{ route('dashboard') }}" class="btn-dashboard">
                    <svg style="width: 20px; height: 20px; margin-right: 8px;" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Ir al Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="btn-dashboard">
                    <svg style="width: 20px; height: 20px; margin-right: 8px;" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    Iniciar Sesión
                </a>
            @endauth

            <button onclick="history.back()" class="btn-back">
                Volver Atrás
            </button>
        </div>
    </div>
@endsection
