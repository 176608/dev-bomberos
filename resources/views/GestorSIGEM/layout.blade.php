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
</style>
@endsection
