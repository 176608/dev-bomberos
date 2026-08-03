@extends('sgu.layouts.admin')

@section('title', 'SGU v2 — Dashboard de Métricas')

@section('sgu_content')
<h3 class="mb-4"><i class="bi bi-bar-chart-fill"></i> Dashboard de Métricas</h3>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body text-center p-4">
                <i class="bi bi-person-video2 display-4 text-success"></i>
                <h5 class="card-title mt-3">Dashboard</h5>
                <p class="card-text text-muted">Métricas de visitas y visitantes del Visor SIGEM v2.</p>
                <span class="btn btn-success disabled" aria-disabled="true">Próximamente</span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body text-center p-4">
                <i class="bi bi-person-gear display-4 text-primary"></i>
                <h5 class="card-title mt-3">Gestor</h5>
                <p class="card-text text-muted">Gestión de usuarios del sistema: alta, edición, roles, estado y PIN de acceso.</p>
                <a href="{{ route('sgu.admin.gestor.usuarios') }}" class="btn btn-primary">Entrar al Gestor</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body text-center p-4">
                <i class="bi bi-shield-lock-fill display-4 text-danger"></i>
                <h5 class="card-title mt-3">Auditor</h5>
                <p class="card-text text-muted">Historial de cambios en usuarios y accesos al sistema (login, logout, intentos).</p>
                <span class="btn btn-danger disabled" aria-disabled="true">Próximamente</span>
            </div>
        </div>
    </div>
</div>
@endsection
