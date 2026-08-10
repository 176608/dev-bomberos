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
@endsection
