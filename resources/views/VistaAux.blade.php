
@extends('layouts.app')

@section('title', 'Vista Auxiliar')

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

@if(auth()->check() && auth()->user()->role === 'Administrador' && isset($users))
    <div class="card mt-4">
        <div class="card-header">
            <h2>Usuarios Registrados</h2>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="usersTable" class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Fecha Registro</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->role }}</td>
                                <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#usersTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
            },
            order: [[0, 'desc']]
        });
    });
</script>
@endsection