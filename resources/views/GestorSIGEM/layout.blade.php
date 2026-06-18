@extends('layouts.app')
<title>@yield('title', 'Módulo Administración SGIEM')</title>
@section('content')
<div class="container-fluid bg-fonde pb-4">
    <nav class="navbar navbar-expand-lg navbar-light bg-light rounded-3 mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('sgiem.admin.index') }}">
                <i class="bi bi-gear"></i> Panel SGIEM
            </a>

            <div class="navbar-nav">
                <a class="nav-link {{ request()->is('sgiem/admin') ? 'active' : '' }}"
                   href="{{ route('sgiem.admin.index') }}">
                    <i class="bi bi-house"></i> Inicio
                </a>
                <a class="nav-link {{ request()->is('sgiem/admin/temas') ? 'active' : '' }}"
                   href="{{ route('sgiem.admin.temas') }}">
                    <i class="bi bi-bookmark"></i> Temas
                </a>
                <a class="nav-link {{ request()->is('sgiem/admin/subtemas') ? 'active' : '' }}"
                   href="{{ route('sgiem.admin.subtemas') }}">
                    <i class="bi bi-bookmarks"></i> Subtemas
                </a>
                <a class="nav-link {{ request()->is('sgiem/admin/cuadros-v2*') ? 'active' : '' }}"
                   href="{{ route('sgiem.admin.cuadros-v2.index') }}">
                    <i class="bi bi-table"></i> Cuadros V2
                </a>
                <a class="nav-link {{ request()->is('sgiem/admin/cuadros') ? 'active' : '' }}"
                   href="{{ route('sgiem.admin.cuadros') }}">
                    <i class="bi bi-table"></i> Cuadros
                </a>
                <a class="nav-link {{ request()->is('sgiem/admin/consultas') ? 'active' : '' }}"
                   href="{{ route('sgiem.admin.consultas') }}">
                    <i class="bi bi-search"></i> Consultas Exprés
                </a>
            </div>
        </div>
    </nav>

    @if(isset($crud_view))
        @include($crud_view)
    @else
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="bi bi-speedometer2"></i> Panel de Administración SGIEM</h4>
                    </div>
                    <div class="card-body">
                        <p>Bienvenido al panel de administración del Sistema de Gestión de Información Estadística Municipal.</p>
                        <p>Selecciona una opción del menú superior para administrar los diferentes módulos.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
.dataTables_wrapper .row:first-child {
    padding-bottom: 1.5rem;
    margin-bottom: 0;
}
.dataTables_wrapper .row:last-child {
    padding-top: 1rem;
    padding-bottom: 1rem;
    margin-top: 0;
}
.dataTables_wrapper .table {
    margin-top: 0.5rem;
    margin-bottom: 0.5rem;
}
.dataTables_wrapper input[type="search"] {
    background-color: #fff !important;
}
.dataTables_wrapper select {
    background-color: #fff !important;
}
.dataTables_wrapper .paginate_button {
    background-color: #fff !important;
}
.dataTables_wrapper .paginate_button.current {
    background-color: #0d6efd !important;
    color: #fff !important;
}
.dataTables_wrapper .paginate_button:hover {
    background-color: #e9ecef !important;
}
.dataTables_wrapper .paginate_button.current:hover {
    background-color: #0b5ed7 !important;
    color: #fff !important;
}
</style>

<div class="toast-container position-fixed bottom-0 start-0 p-3" id="sgiemToastContainer" style="z-index: 9999;"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
        mostrarToast('success', '{{ session('success') }}');
    @endif
    @if(session('error'))
        mostrarToast('danger', '{{ session('error') }}');
    @endif
    @if($errors->any())
        mostrarToast('warning', 'Errores de validación: Corrige los campos marcados.');
    @endif
});

function mostrarToast(type, message) {
    const container = document.getElementById('sgiemToastContainer');
    const icons = {
        success: 'bi-check-circle-fill',
        danger: 'bi-exclamation-circle-fill',
        warning: 'bi-exclamation-triangle-fill',
        info: 'bi-info-circle-fill'
    };
    const titleText = {
        success: 'Éxito',
        danger: 'Error',
        warning: 'Advertencia',
        info: 'Información'
    };
    const toastId = 'toast-' + Date.now();
    const toast = document.createElement('div');
    toast.id = toastId;
    toast.className = 'toast';
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    toast.setAttribute('data-bs-delay', '60000');
    toast.innerHTML = `
        <div class="toast-header text-white bg-${type}">
            <i class="bi ${icons[type] || icons.info} me-2"></i>
            <strong class="me-auto">${titleText[type] || 'Mensaje'}</strong>
            <small>ahora</small>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
        </div>
        <div class="toast-body">${message}</div>
    `;
    container.appendChild(toast);
    const bsToast = new bootstrap.Toast(toast, { autohide: true, delay: 60000 });
    bsToast.show();
    toast.addEventListener('hidden.bs.toast', function() { this.remove(); });
}
</script>
@endsection
