<!-- Archivo Bomberos - NO ELIMINAR COMENTARIO -->
@extends('layouts.app')

@section('title', 'Panel de Administrador')

@section('content')
<div class="card py-4">
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
            <table id="usersTable" class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Acceso</th>
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
                                        <span class="badge bg-warning">Nueva</span>
                                        @break
                                    @case(2)
                                        <span class="badge bg-info">Cambio</span>
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
                    <h5 class="modal-title">Alta de Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Correo</label>
                        <input type="email" class="form-control" name="email" required>
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
                <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Usuario</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nombre</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   name="name" value="{{ old('name', $user->name) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Correo</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   name="email" value="{{ old('email', $user->email) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Rol</label>
                            <select class="form-select @error('role') is-invalid @enderror" name="role" required>
                                <option value="Capturista" {{ $user->role === 'Capturista' ? 'selected' : '' }}>Capturista</option>
                                <option value="Desarrollador" {{ $user->role === 'Desarrollador' ? 'selected' : '' }}>Desarrollador</option>
                                <option value="Administrador" {{ $user->role === 'Administrador' ? 'selected' : '' }}>Administrador</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Estado</label>
                            <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                                <option value="1" {{ $user->status ? 'selected' : '' }}>Activo</option>
                                <option value="0" {{ !$user->status ? 'selected' : '' }}>Inactivo</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="resetPassword{{ $user->id }}" 
                                       name="reset_password">
                                <label class="form-check-label" for="resetPassword{{ $user->id }}">
                                    Resetear contraseña de usuario
                                </label>
                            </div>
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
    // Initialize DataTable
    const table = $('#usersTable').DataTable({
        language: {
            url: "{{ asset('js/datatables/i18n/es-ES.json') }}"
        },
        processing: true,
        order: [[0, 'desc']],
        columnDefs: [
            {
                targets: '_all',
                defaultContent: ''
            }
        ]
    });

    // Handle form submission
    $('form[action*="/admin/users/"]').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const modal = form.closest('.modal');

        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Hide modal
                    modal.modal('hide');
                    
                    alert(response.message);
                    window.location.reload();
                }
            },
            error: function(xhr) {
                let errorMessage = 'Error al actualizar usuario';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                alert(errorMessage);
            }
        });
    });

    // Remove DataTable warnings from console
    $.fn.dataTable.ext.errMode = 'none';
    
    // Handle DataTable errors more gracefully
    table.on('error.dt', function(e, settings, techNote, message) {
        console.error('DataTables error:', message);
    });
});
</script>
@endsection