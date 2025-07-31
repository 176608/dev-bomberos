<!-- Archivo Bomberos - NO ELIMINAR COMENTARIO -->
@extends('layouts.app')

@section('title', 'Consultor de Hidrantes')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">
                        <i class="bi bi-binoculars-fill me-2"></i>
                        Sistema Consultor de Hidrantes
                    </h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Panel de Consulta Público</strong> - Aquí podrás consultar información sobre hidrantes.
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h5>
                                <i class="bi bi-droplet-fill text-primary me-2"></i>
                                Funcionalidades Disponibles
                            </h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <i class="bi bi-search text-success me-2"></i>
                                    Búsqueda de hidrantes por ubicación
                                </li>
                                <li class="list-group-item">
                                    <i class="bi bi-map text-success me-2"></i>
                                    Visualización en mapa interactivo
                                </li>
                                <li class="list-group-item">
                                    <i class="bi bi-list-ul text-success me-2"></i>
                                    Listado detallado de hidrantes
                                </li>
                                <li class="list-group-item">
                                    <i class="bi bi-info-square text-success me-2"></i>
                                    Información técnica detallada
                                </li>
                            </ul>
                        </div>
                        
                        <div class="col-md-6">
                            <h5>
                                <i class="bi bi-gear-fill text-warning me-2"></i>
                                Estado del Sistema
                            </h5>
                            <div class="alert alert-warning">
                                <i class="bi bi-tools me-2"></i>
                                <strong>En Desarrollo (WIP)</strong>
                                <br>
                                <small>Este módulo está siendo desarrollado. Próximamente estará disponible.</small>
                            </div>
                            
                            <div class="d-grid gap-2">
                                @auth
                                    @if(in_array(auth()->user()->role, ['Capturista', 'Administrador', 'Desarrollador']))
                                        <a href="{{ route('capturista.panel') }}" class="btn btn-primary">
                                            <i class="bi bi-droplet-fill me-2"></i>
                                            Ir al Panel de Hidrantes
                                        </a>
                                    @endif
                                @endauth
                                
                                <a href="{{ route('sigem.laravel.public') }}" class="btn btn-success">
                                    <i class="bi bi-map me-2"></i>
                                    Ver Sistema SIGEM
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
console.log('Dashboard de Consultor cargado correctamente');

// Aquí irá la lógica del consultor cuando esté implementado
document.addEventListener('DOMContentLoaded', function() {
    console.log('Sistema Consultor de Hidrantes - En desarrollo');
});
</script>
@endsection