<!-- Archivo Bomberos - NO ELIMINAR COMENTARIO -->
@extends('layouts.app')

@section('title', 'Sistema registro de Vías y Zonas')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="text-primary">
                    <i class="bi bi-journal-text"></i> 
                    Sistema de Registro de Vías y Zonas
                </h2>
                <div class="text-muted">
                    <small>
                        <i class="bi bi-person-circle"></i> {{ Auth::user()->name ?? Auth::user()->email }}
                        <span class="badge bg-info ms-2">{{ Auth::user()->role }}</span>
                    </small>
                </div>
            </div>

            <!-- Panel principal -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-map"></i> 
                        Panel de Control - Vías y Zonas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <!-- Sección Colonias -->
                        <div class="col-md-6">
                            <div class="card h-100 border-success">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0">
                                        <i class="bi bi-houses"></i> 
                                        Gestión de Colonias
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted">Administra las colonias del sistema</p>
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-outline-success" type="button">
                                            <i class="bi bi-plus-circle"></i> Nueva Colonia
                                        </button>
                                        <button class="btn btn-outline-primary" type="button">
                                            <i class="bi bi-list-ul"></i> Ver Colonias
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sección Calles -->
                        <div class="col-md-6">
                            <div class="card h-100 border-warning">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="mb-0">
                                        <i class="bi bi-signpost"></i> 
                                        Gestión de Calles
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted">Administra las calles del sistema</p>
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-outline-warning" type="button">
                                            <i class="bi bi-plus-circle"></i> Nueva Calle
                                        </button>
                                        <button class="btn btn-outline-primary" type="button">
                                            <i class="bi bi-list-ul"></i> Ver Calles
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Estadísticas -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0">
                                        <i class="bi bi-graph-up"></i> 
                                        Estadísticas Rápidas
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-md-6">
                                            <div class="border-end">
                                                <h4 class="text-success">--</h4>
                                                <small class="text-muted">Total Colonias</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h4 class="text-warning">--</h4>
                                            <small class="text-muted">Total Calles</small>
                                        </div>
                                    </div>
                                </div>
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
$(document).ready(function() {
    console.log('Panel Registrador cargado correctamente');
});
</script>
@endsection