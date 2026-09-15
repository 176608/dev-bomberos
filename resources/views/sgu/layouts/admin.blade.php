@extends('layouts.app')

@section('title', 'SGU v2 — ' . ($titulo ?? 'Administración'))

@section('content')
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #2c5f4a;">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('sgu.admin.index') }}">
            <i class="bi bi-people-fill"></i> SGU v2
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sguNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="sguNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('sgu.admin.index') ? 'active' : '' }}"
                       href="{{ route('sgu.admin.index') }}">
                        <i class="bi bi-bar-chart-fill"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('sgu.admin.gestor*') ? 'active' : '' }}"
                       href="{{ route('sgu.admin.gestor.usuarios') }}">
                        <i class="bi bi-person-gear"></i> Gestor
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('sgu.admin.auditor*') ? 'active' : '' }}"
                       href="{{ route('sgu.admin.auditor.accesos') }}">
                        <i class="bi bi-shield-lock-fill"></i> Auditor
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container-fluid bg-fonde pt-4 pb-4">
    @yield('sgu_content')
</div>

<style>
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 12px;
    }
    .dataTables_wrapper .dataTables_length {
        margin-bottom: 12px;
    }
</style>

{{-- B6 (doc 16): patrón único de alertas en SGU — flash de sesión → toast global --}}
<div class="toast-container position-fixed bottom-0 start-0 p-3" id="sguToastContainer" style="z-index: 9999;"></div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
        mostrarToast('success', {!! Js::from(session('success')) !!});
    @endif
    @if(session('error'))
        mostrarToast('danger', {!! Js::from(session('error')) !!});
    @endif
});

function mostrarToast(type, message) {
    const container = document.getElementById('sguToastContainer');
    const esc = function (s) {
        return String(s ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    };
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
        <div class="toast-body">${esc(message)}</div>
    `;
    container.appendChild(toast);
    const bsToast = new bootstrap.Toast(toast, { autohide: true, delay: 60000 });
    bsToast.show();
    toast.addEventListener('hidden.bs.toast', function() { this.remove(); });
}
</script>
@endsection
