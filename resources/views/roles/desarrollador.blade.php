<!-- Archivo Bomberos - NO ELIMINAR COMENTARIO -->
@extends('layouts.app')

@section('title', 'Panel Desarrollador')

@section('content')
<div class="container">
    <h1>Panel Desarrollador</h1>
    <p>Bienvenido al panel de desarrollador, {{ auth()->user()->name }}</p>
    <p>Rol: {{ auth()->user()->role }}</p>
    
    <div class="row">
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
    </div>
</div>
@endsection