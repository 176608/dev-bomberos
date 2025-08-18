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
                        <!-- Sección Zonas -->
                        <div class="col-md-6">
                            <div class="card h-100 border-success">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0">
                                        <i class="bi bi-houses"></i> 
                                        Gestión de Zonas
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted">Administra las Zonas del sistema</p>
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-outline-success" type="button">
                                            <i class="bi bi-plus-circle"></i> Nueva Zona
                                        </button>
                                        <button class="btn btn-outline-primary" type="button">
                                            <i class="bi bi-list-ul"></i> Ver Zonas
                                        </button>
                                    </div>
                                    <div class="col">
                                        <div class="border-end">
                                            <h4 class="text-success">--</h4>
                                            <small class="text-muted">Total Zonas</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sección Vías -->
                        <div class="col-md-6">
                            <div class="card h-100 border-warning">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="mb-0">
                                        <i class="bi bi-signpost"></i> 
                                        Gestión de Vías
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted">Administra las Vías del sistema</p>
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-outline-warning" type="button">
                                            <i class="bi bi-plus-circle"></i> Nueva Vía
                                        </button>
                                        <button class="btn btn-outline-primary" type="button">
                                            <i class="bi bi-list-ul"></i> Ver Vías
                                        </button>
                                    </div>
                                    <div class="col">
                                        <h4 class="text-warning">--</h4>
                                        <small class="text-muted">Total Vías</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Estadísticas -->
                    <div class="row mt-4">
                        <div class="col-12">
                            TABLA Zonas / Calles Yajra
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