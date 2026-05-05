@extends('layouts.app')

@section('title', 'Historial de Eliminados')

@section('content')
<div class="container mt-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-clock-history"></i> Historial de Dictámenes Eliminados</h2>
        <a href="{{ route('sg-dictamen.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover table-bordered">
            <thead class="table-dark">
                <tr>
                    <th># Registro</th>
                    <th>Fecha</th>
                    <th># Oficio</th>
                    <th>Dependencia</th>
                    <th>Nombre / Puesto</th>
                    <th>Asunto</th>
                    <th>Núm. Oficio</th>
                    <th>Revisado por</th>
                    <th>Observaciones</th>
                    <th>Estatus Anterior</th>
                    <th>Eliminado Por (ID)</th>
                    <th>Fecha de Eliminación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($deletedLogs as $log)
                <tr>
                    <td>{{ $log->dictamen_id }}</td>
                    <td>{{ $log->fecha ? \Carbon\Carbon::parse($log->fecha)->format('d/m/Y') : 'N/A' }}</td>
                    <td>{{ $log->oficio ?? 'S/N' }}</td>
                    <td>{{ $log->dependencia_empres ?? 'N/A' }}</td>
                    <td>{{ $log->nombre_puesto ?? 'N/A' }}</td>
                    <td title="{{ $log->asunto }}">{{ \Illuminate\Support\Str::limit($log->asunto, 50) }}</td>
                    <td>{{ $log->numero_oficio ?? 'N/A' }}</td>
                    <td>{{ $log->revisado_por ?? 'N/A' }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($log->observaciones, 30) ?? 'N/A' }}</td>
                    <td>
                        <span class="badge bg-danger">{{ $log->estatus }}</span>
                    </td>
                    <td>{{ $log->deleted_by }}</td>
                    <td>{{ \Carbon\Carbon::parse($log->deleted_at)->format('d/m/Y H:i') }}</td>
                    <td>
    <form action="{{ route('sg-dictamen.restore', $log->id) }}" method="POST" style="display:inline;">
        @csrf
        <button type="submit" class="btn btn-sm btn-outline-success" onclick="return confirm('¿Estás seguro que deseas restaurar este dictamen?');">
            <i class="bi bi-arrow-counterclockwise"></i> Restaurar
        </button>
    </form>
</td>
                </tr>
                @empty
                <tr>
                    <td colspan="12" class="text-center text-muted">No hay registros eliminados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection