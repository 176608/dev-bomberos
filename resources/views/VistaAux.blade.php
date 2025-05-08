@extends('layouts.app')

@section('title', 'Prueba de Conexión')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Prueba de Conexión SQL Server</h2>
    </div>
    <div class="card-body">
        <div class="alert alert-{{ $status == 'success' ? 'success' : 'danger' }}" role="alert">
            {{ $message }}
        </div>
    </div>
</div>
@endsection