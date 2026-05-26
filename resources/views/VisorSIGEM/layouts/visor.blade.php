<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIGEM v2 — Visor Estadístico Municipal')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/estadistica.css') }}">
    @stack('styles')
</head>
<body>

    {{-- ========== HEADER LOGOS ========== --}}
    <div class="container-fluid">
        <div class="header-logos">
            <div class="logo-section">
                <img src="{{ asset('imagenes/logoadmin.png') }}" alt="JRZ Logo">
            </div>
            <div class="logo-section">
                <img src="{{ asset('imagenes/sige2.png') }}" alt="SIGEM Logo">
            </div>
        </div>
    </div>

    {{-- ========== NAVEGACIÓN PRINCIPAL ========== --}}
    @php
        $currentRoute = request()->route()?->getName() ?? '';
    @endphp

    <div class="main-menu container-fluid p-0">
        <div class="nav-container">
            <a href="{{ route('sigem.v2.index') }}"
               class="sigem-nav-link {{ str_starts_with($currentRoute, 'sigem.v2.index') ? 'active' : '' }}">
                <i class="bi bi-house-fill"></i> INICIO
            </a>
            <a href="{{ route('sigem.v2.catalogo') }}"
               class="sigem-nav-link {{ str_starts_with($currentRoute, 'sigem.v2.catalogo') ? 'active' : '' }}">
                <i class="bi bi-journal-text"></i> CATÁLOGO
            </a>
            <a href="{{ route('sigem.v2.estadistica') }}"
               class="sigem-nav-link {{ str_starts_with($currentRoute, 'sigem.v2.estadistica') ? 'active' : '' }}">
                <i class="bi bi-bar-chart-fill"></i> ESTADÍSTICA
            </a>
            <a href="{{ route('sigem.v2.cartografia') }}"
               class="sigem-nav-link {{ str_starts_with($currentRoute, 'sigem.v2.cartografia') ? 'active' : '' }}">
                <i class="bi bi-map-fill"></i> CARTOGRAFÍA
            </a>
            <a href="{{ route('sigem.v2.productos') }}"
               class="sigem-nav-link {{ str_starts_with($currentRoute, 'sigem.v2.productos') ? 'active' : '' }}">
                <i class="bi bi-box-seam"></i> PRODUCTOS
            </a>
        </div>
    </div>

    {{-- ========== CONTENIDO PRINCIPAL ========== --}}
    <div class="container my-4">
        @yield('content')
    </div>

    {{-- ========== FOOTER ========== --}}
    <footer class="bg-light text-center text-muted py-3 mt-5 border-top">
        <small>SIGEM — Sistema de Información Geográfica y Estadística Municipal &copy; {{ date('Y') }}</small>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
