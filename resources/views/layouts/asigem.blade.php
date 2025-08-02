<!-- Archivo SIGEM - Base de vista sigem publica - NO ELIMINAR COMENTARIO -->
@extends('layouts.app')
@section('title', 'SIGEM - Sistema de Información Geográfica')

@section('content')
    <style>
        .header-logos {
            display: flex;
            width: 100%;
            min-height: 100px;
            border-bottom: 4px solid #ffd700;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .logo-section {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: white;
            margin: 10px 5px;
            border-radius: 8px;
            padding: 15px;
            transition: all 0.3s ease;
        }

        .logo-section:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }

        .logo-section img {
            max-width: 100%;
            max-height: 80px;
            object-fit: contain;
        }

        /* Menú SIGEM */
        .main-menu {
            background-color: #2a6e48 !important;
            border-bottom: 3px solid #ffd700;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            width: 100%;
            margin: 0;
            padding: 0;
        }

        .main-menu .nav-container {
            display: flex;
            justify-content: center;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0;
        }

        .main-menu a {
            color: white;
            text-decoration: none;
            padding: 12px 20px;
            font-weight: bold;
            font-size: 14px;
            border-radius: 0;
            transition: all 0.3s ease;
            position: relative;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .main-menu a:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: #ffd700;
            transform: translateY(-1px);
        }

        .main-menu a.active {
            background-color: #1e4d35;
            color: #ffd700;
            font-weight: bold;
        }

        .main-menu a.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background-color: #ffd700;
        }

        /* Cargando indicator */
        .Cargando {
            text-align: center;
            padding: 40px;
            color: #2a6e48;
        }

        .Cargando i {
            font-size: 24px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Efectos para focus */
        .highlight-focus {
            background-color: #fff3cd !important;
            border: 2px solid #ffc107 !important;
            box-shadow: 0 0 15px rgba(255, 193, 7, 0.5) !important;
            animation: pulseHighlight 1s ease-in-out;
        }

        @keyframes pulseHighlight {
            0% { transform: scale(1); }
            50% { transform: scale(1.02); }
            100% { transform: scale(1); }
        }

        /* Estilos para productos */
        .product-section {
            display: flex;
            align-items: flex-start;
            gap: 20px;
            margin-bottom: 20px;
        }

        .product-section img {
            max-width: 200px;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .product-text {
            flex: 1;
        }

        /* Estilos para catálogo */
        .catalogo-row {
            min-height: 600px;
        }

        .catalogo-row .card-body {
            padding: 0;
        }

        .catalogo-row .card-body > div {
            height: 550px;
            overflow-y: auto;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header-logos {
                flex-direction: column;
                min-height: auto;
            }
            
            .main-menu .nav-container {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .main-menu a {
                padding: 10px 15px;
                font-size: 13px;
            }

            .product-section {
                flex-direction: column;
                text-align: center;
            }

            .product-section img {
                max-width: 100%;
            }
        }
    </style>

    @php
        $img1 = asset('imagenes/logoadmin.png');
        $img2 = asset('imagenes/sige2.png');
    @endphp

    <div class="container-fluid" style="background: linear-gradient(135deg, #2a6e48 0%, #66d193 50%, #2a6e48 100%);">
        <!-- Sección de Logos -->
        <div class="header-logos container-fluid">
            <div class="logo-section">
                <img src="{{ $img1 }}" alt="JRZ Logo">
            </div>
            <div class="logo-section">
                <img src="{{ $img2 }}" alt="SIGEM Logo">
            </div>
        </div>

        <!-- MENÚ SIGEM -->
        <div class="main-menu container-fluid p-0">
            <div class="nav-container">
                <a href="#" data-section="inicio" class="sigem-nav-link active">
                    <i class="bi bi-house-fill"></i> INICIO
                </a>
                <a href="#" data-section="catalogo" class="sigem-nav-link">
                    <i class="bi bi-journal-text"></i> CATÁLOGO
                </a>
                <a href="#" data-section="estadistica" class="sigem-nav-link">
                    <i class="bi bi-bar-chart-fill"></i> ESTADÍSTICA
                </a>
                <a href="#" data-section="cartografia" class="sigem-nav-link">
                    <i class="bi bi-map-fill"></i> CARTOGRAFÍA
                </a>
                <a href="#" data-section="productos" class="sigem-nav-link">
                    <i class="bi bi-box-seam"></i> PRODUCTOS
                </a>
            </div>
        </div>

        <!-- CONTENEDOR DINÁMICO -->
        <div id="sigem-content" class="container my-4">
            @yield('dynamic_content')
        {{-- Asegúrate de que esta parte exista en la vista principal --}}
        @if(isset($section) && $section === 'estadistica')
            @include('partials.estadistica')
        @endif
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/sigem.js') }}"></script>
@endsection