<!-- SIGEM PUNTO BLADE PUNTO PHP -->
@extends('layouts.app')
<title>@yield('title', 'Módulo Administración SIGEM')</title>
@section('content')
<div class="container-fluid bg-fonde">
    <!-- Navbar sencilla de administración -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('sigem.admin.index') }}">
                <i class="bi bi-gear"></i> Panel SIGEM Admin
            </a>
            
            <div class="navbar-nav">
                <a class="nav-link {{ request()->is('sigem/admin') ? 'active' : '' }}" 
                   href="{{ route('sigem.admin.index') }}">
                    <i class="bi bi-house"></i> Inicio
                </a>
                <a class="nav-link {{ request()->is('sigem/admin/mapas') ? 'active' : '' }}" 
                   href="{{ route('sigem.admin.mapas') }}">
                    <i class="bi bi-map"></i> Mapas
                </a>
                <a class="nav-link {{ request()->is('sigem/admin/temas') ? 'active' : '' }}" 
                   href="{{ route('sigem.admin.temas') }}">
                    <i class="bi bi-bookmark"></i> Temas
                </a>
                <a class="nav-link {{ request()->is('sigem/admin/subtemas') ? 'active' : '' }}" 
                   href="{{ route('sigem.admin.subtemas') }}">
                    <i class="bi bi-bookmarks"></i> Subtemas
                </a>
                <a class="nav-link {{ request()->is('sigem/admin/cuadros') ? 'active' : '' }}" 
                   href="{{ route('sigem.admin.cuadros') }}">
                    <i class="bi bi-table"></i> Cuadros
                </a>
                <a class="nav-link {{ request()->is('sigem/admin/consultas') ? 'active' : '' }}" 
                   href="{{ route('sigem.admin.consultas') }}">
                    <i class="bi bi-search"></i> Consultas Express
                </a>
            </div>
        </div>
    </nav>

    <!-- Contenido dinámico -->
    @if(isset($crud_view))
        @include($crud_view)
    @else
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="bi bi-speedometer2"></i> Panel de Administración SIGEM</h4>
                    </div>
                    <div class="card-body">
                        <p>Bienvenido al panel de administración del Sistema de Información Geográfica y Estadística Municipal.</p>
                        <p>Selecciona una opción del menú superior para administrar los diferentes módulos.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
