@extends('layouts.app')

@section('title', 'Panel de Administrador')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h2>Panel de Administrador</h2>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
            Crear Usuario
        </button>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        <div class="table-responsive">
            <table id="usersTable" class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Fecha Registro</th>
                        <th>Acciones</th>
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
                            <td>
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}">
                                    Editar
                                </button>
                                @if($user->id !== auth()->id())
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro?')">
                                            Eliminar
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create User Modal -->
<div class="modal fade" id="createUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Crear Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contraseña</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rol</label>
                        <select class="form-select" name="role" required>
                            <option value="Analista">Analista</option>
                            <option value="Desarrollador">Desarrollador</option>
                            <option value="Administrador">Administrador</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modals -->
@foreach($users as $user)
    <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Usuario</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nombre</label>
                            <input type="text" class="form-control" name="name" value="{{ $user->name }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" value="{{ $user->email }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contraseña (dejar en blanco para mantener)</label>
                            <input type="password" class="form-control" name="password">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Rol</label>
                            <select class="form-select" name="role" required>
                                <option value="Analista" {{ $user->role === 'Analista' ? 'selected' : '' }}>Analista</option>
                                <option value="Desarrollador" {{ $user->role === 'Desarrollador' ? 'selected' : '' }}>Desarrollador</option>
                                <option value="Administrador" {{ $user->role === 'Administrador' ? 'selected' : '' }}>Administrador</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

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