@extends('layouts.app')

@section('title')@yield('visor_title', 'SIGEM v2 — Visor Estadístico Municipal')@endsection

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

    .header-logos > a {
        flex: 1;
        display: flex;
        text-decoration: none;
    }

    .header-logos > a:hover .logo-section {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.15);
    }

    .logo-section img {
        max-width: 100%;
        max-height: 80px;
        object-fit: contain;
    }

    .main-menu {
        background-color: #48887B !important;
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

    .main-menu a,
    .main-menu .nav-link-disabled {
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
        background-color: rgba(255,255,255,0.1);
        color: #ffd700;
        transform: translateY(-1px);
    }

    .main-menu a.active {
        background-color: #0b584fff;
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

    .main-menu .nav-link-disabled {
        color: rgba(255,255,255,0.5);
        cursor: default;
        font-weight: 600;
        border-left: 1px solid rgba(255,255,255,0.15);
    }

    .main-menu .nav-link-disabled:hover {
        background-color: transparent;
        color: rgba(255,255,255,0.5);
        transform: none;
    }

    .main-menu .nav-link-disabled .maintenance-text {
        font-size: 8px;
        font-weight: 400;
        opacity: 0.6;
        line-height: 1;
        letter-spacing: 0.3px;
    }

    .main-menu .nav-link-disabled i {
        font-size: 16px;
    }

    @media (max-width: 768px) {
        .header-logos {
            flex-direction: column;
            min-height: auto;
        }
        .main-menu .nav-container {
            flex-wrap: wrap;
            justify-content: center;
        }
        .main-menu a,
        .main-menu .nav-link-disabled {
            padding: 10px 15px;
            font-size: 13px;
        }
    }
</style>

@php
    $currentRoute = request()->route()?->getName() ?? '';
@endphp

<div class="container-fluid pb-5 bg-fonde img-fluid">
    <div class="header-logos container-fluid">
        <a href="https://www.imip.org.mx" target="_blank" class="text-decoration-none">
            <div class="logo-section">
                <img src="{{ asset('imagenes/IMIP_icon_text.png') }}" alt="IMIP Logo">
            </div>
        </a>
        <a href="https://www.imip.org.mx" target="_blank" class="text-decoration-none">
            <div class="logo-section">
                <img src="{{ asset('imagenes/sige2.png') }}" alt="SIGEM Logo">
            </div>
        </a>
    </div>

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

    <div class="container my-4">
        @yield('visor_content')
    </div>
</div>
@endsection

@section('scripts')
    @parent
    @stack('visor_scripts')
@endsection
