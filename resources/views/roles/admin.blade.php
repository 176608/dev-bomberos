@extends('layouts.app')

@section('title', 'Panel de Administrador')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <h2>Panel de Administrador</h2>
            <select class="form-select" style="width: auto;" onchange="window.location.href='{{ route('admin.panel') }}?status=' + this.value">
                <option value="active" {{ $status == 'active' ? 'selected' : '' }}>Usuarios Activos</option>
                <option value="inactive" {{ $status == 'inactive' ? 'selected' : '' }}>Usuarios Inactivos</option>
                <option value="all" {{ $status == 'all' ? 'selected' : '' }}>Todos los Usuarios</option>
            </select>
        </div>
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
                        <th>Email Verificado</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Estado Login</th>
                        <th>Fecha Registro</th>
                        <th>Última Edición</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->email_verified_at)
                                    <span class="badge bg-success">Verificado</span>
                                @else
                                    <span class="badge bg-warning">No verificado</span>
                                @endif
                            </td>
                            <td>{{ $user->role }}</td>
                            <td>
                                <span class="badge bg-{{ $user->status ? 'success' : 'danger' }}">
                                    {{ $user->status ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td>
                                @switch($user->log_in_status)
                                    @case(0)
                                        <span class="badge bg-success">Normal</span>
                                        @break
                                    @case(1)
                                        <span class="badge bg-warning">Nueva cuenta</span>
                                        @break
                                    @case(2)
                                        <span class="badge bg-info">Cambio solicitado</span>
                                        @break
                                @endswitch
                            </td>
                            <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $user->updated_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}">
                                    Editar
                                </button>
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
                        <input type="password" class="form-control" name="password" required minlength="8" pattern=".{8,}" title="La contraseña debe tener al menos 8 caracteres">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rol</label>
                        <select class="form-select" name="role" required>
                            <option value="Capturista">Capturista</option>
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
                            <input type="password" class="form-control" name="password" 
                                pattern="^$|.{8,}"
                                title="La contraseña debe estar vacía o tener al menos 8 caracteres"
                                oninput="this.setCustomValidity('')"
                                oninvalid="this.setCustomValidity('La contraseña debe estar vacía o tener al menos 8 caracteres')">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Rol</label>
                            <select class="form-select" name="role" required>
                                <option value="Capturista" {{ $user->role === 'Capturista' ? 'selected' : '' }}>Capturista</option>
                                <option value="Desarrollador" {{ $user->role === 'Desarrollador' ? 'selected' : '' }}>Desarrollador</option>
                                <option value="Administrador" {{ $user->role === 'Administrador' ? 'selected' : '' }}>Administrador</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Estado</label>
                            <select class="form-select" name="status" required>
                                <option value="1" {{ $user->status ? 'selected' : '' }}>Activo</option>
                                <option value="0" {{ !$user->status ? 'selected' : '' }}>Inactivo</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Estado de Login</label>
                            <select class="form-select" name="log_in_status" required>
                                <option value="0" {{ $user->log_in_status === 0 ? 'selected' : '' }}>Normal</option>
                                <option value="1" {{ $user->log_in_status === 1 ? 'selected' : '' }}>Nueva cuenta</option>
                                <option value="2" {{ $user->log_in_status === 2 ? 'selected' : '' }}>Cambio solicitado</option>
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
                url: "{{ asset('js/datatables/i18n/es-ES.json') }}"
            },
            order: [[0, 'desc']]
        });
    });
</script>
@endsection