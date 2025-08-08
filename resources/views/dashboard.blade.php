<!-- Archivo Bomberos -index- NO ELIMINAR COMENTARIO -->
@extends('layouts.app')

@section('title', 'Consultor de Hidrantes')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            <div class="card shadow-sm" style="background-color: rgba(186, 216, 184, 0.8);">
                <div class="card-header bg-success text-white">
                    <h3 class="mb-0">
                        <i class="bi bi-binoculars-fill me-2"></i>
                        Sistema Consultor de Hidrantes
                    </h3>
                </div>
                <div class="card-body">
                    
                    <div class="row mb-4">
                        <div class="col-md-8    ">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="bi bi-search me-2"></i>Consulta de Hidrantes</h5>
                                </div>
                                <div class="card-body">
                                    <form id="consultaHidranteForm" method="GET" action="{{ route('consultor.buscar') }}">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">Introduce el número del hidrante para ver su reporte:</span>
                                            <input type="number" class="form-control" id="hidrante_id" name="hidrante_id" min="1" required>
                                            <button class="btn btn-success" type="submit">
                                                <i class="bi bi-search me-1"></i>Buscar
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Panel Auxiliar</h5>
                                </div>
                                <div class="card-body">
                                    <div class="text-center">
                                        <h5>Total de hidrantes:</h5>
                                        <h4 class="text-success">{{ $totalHidrantes }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0"><i class="bi bi-clipboard-data me-2"></i>Reporte de Hidrante</h5>
                                    @if(isset($hidrante))
                                    <span class="badge bg-success">ID: {{ $hidrante->id }}</span>
                                    @endif
                                </div>
                                <div class="card-body" id="resultadoConsulta">
                                    @if(isset($hidrante))
                                        @include('partials.hidrante-consulta', ['hidrante' => $hidrante, 'readOnly' => true])
                                    @elseif(isset($error))
                                        <div class="alert alert-danger">
                                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                            {{ $error }}
                                        </div>
                                    @else
                                        <div class="alert alert-info">
                                            <i class="bi bi-info-circle me-2"></i>
                                            Ingresa un ID de hidrante para consultar su información.
                                        </div>
                                    @endif
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
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert-dismissible');
        alerts.forEach(function(alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
    
    document.getElementById('hidrante_id').focus();
    
    document.getElementById('consultaHidranteForm').addEventListener('submit', function(e) {
        const hidranteId = document.getElementById('hidrante_id').value;
        if (!hidranteId || isNaN(parseInt(hidranteId)) || parseInt(hidranteId) <= 0) {
            e.preventDefault();
            alert('Por favor, introduce un ID válido para el hidrante.');
        }
    });
});
</script>
@endsection