<!-- Archivo Bomberos - NO ELIMINAR COMENTARIO -->
@extends('layouts.app')

@section('title', 'Panel Desarrollador')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="bg-warning text-dark p-4 rounded mb-4">
                <h1 class="h2 mb-0">
                    <i class="bi bi-code-slash"></i> Panel Desarrollador
                </h1>
                <p class="mb-0">Acceso completo al sistema y herramientas de desarrollo</p>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Accesos existentes del desarrollador --}}
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5>Acceso a Admin</h5>
                    <a href="{{ route('admin.panel') }}" class="btn btn-primary">Ir a Admin</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5>Acceso a Capturista</h5>
                    <a href="{{ route('capturista.panel') }}" class="btn btn-success">Ir a Bomberos</a>
                </div>
            </div>
        </div>
        
        {{-- NUEVA SECCIÓN: SIGEM Laravel --}}
        <div class="col-12">
            <hr class="my-4">
            <h3 class="h4 mb-3">
                <i class="bi bi-geo-alt text-success"></i> SIGEM Laravel (Sistema Nuevo)
            </h3>
        </div>
        
        <div class="col-md-6 mb-3">
            <div class="card h-100 border-success">
                <div class="card-body text-center">
                    <i class="bi bi-geo-alt display-4 text-success mb-3"></i>
                    <h5 class="card-title">SIGEM Público</h5>
                    <p class="card-text">Vista pública del Sistema de Información Geográfica Laravel</p>
                    <a href="{{ route('sigem.laravel.public') }}" class="btn btn-success">
                        <i class="bi bi-eye"></i> Abrir SIGEM Público
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <div class="card h-100 border-success">
                <div class="card-body text-center">
                    <i class="bi bi-shield-check display-4 text-success mb-3"></i>
                    <h5 class="card-title">SIGEM Admin</h5>
                    <p class="card-text">Panel administrativo del Sistema SIGEM Laravel</p>
                    <a href="{{ route('sigem.laravel.admin') }}" class="btn btn-success">
                        <i class="bi bi-tools"></i> Abrir SIGEM Admin
                    </a>
                </div>
            </div>
        </div>
        
        {{-- SECCIÓN: SIGEM Original --}}
        <div class="col-12">
            <hr class="my-4">
            <h3 class="h4 mb-3">
                <i class="bi bi-geo text-primary"></i> SIGEM Original (Sistema PHP)
            </h3>
        </div>
        
        <div class="col-md-6 mb-3">
            <div class="card h-100 border-primary">
                <div class="card-body text-center">
                    <i class="bi bi-geo display-4 text-primary mb-3"></i>
                    <h5 class="card-title">SIGEM Original Público</h5>
                    <p class="card-text">Sistema original PHP en funcionamiento</p>
                    <a href="/m_aux/public/vistas_SIGEM/cartografia.php" class="btn btn-primary" target="_blank">
                        <i class="bi bi-eye"></i> Abrir Original
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <div class="card h-100 border-primary">
                <div class="card-body text-center">
                    <i class="bi bi-tools display-4 text-primary mb-3"></i>
                    <h5 class="card-title">SIGEM Admin Original</h5>
                    <p class="card-text">Panel administrativo del sistema original</p>
                    <a href="#" class="btn btn-primary" target="_blank">
                        <i class="bi bi-tools"></i> Admin Original
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection