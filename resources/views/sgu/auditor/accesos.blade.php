@extends('sgu.layouts.admin')

@section('title', 'SGU v2 — Auditor: Accesos al sistema')

@section('sgu_content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h4 class="mb-0"><i class="bi bi-shield-lock-fill"></i> Auditor — Accesos al sistema</h4>
    <div class="btn-group" role="group">
        <a href="{{ route('sgu.admin.auditor.accesos') }}"
           class="btn btn-sm btn-danger {{ request()->routeIs('sgu.admin.auditor.accesos') ? 'active' : '' }}">
            <i class="bi bi-box-arrow-in-right"></i> Accesos
        </a>
        <a href="{{ route('sgu.admin.auditor.usuarios') }}"
           class="btn btn-sm btn-outline-danger {{ request()->routeIs('sgu.admin.auditor.usuarios') ? 'active' : '' }}">
            <i class="bi bi-pencil-square"></i> Cambios en usuarios
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Registro de accesos</span>
        <select id="filtroAccion" class="form-select form-select-sm" style="width:auto;">
            <option value="">Todas las acciones</option>
            @foreach($etiquetas as $accion => $datos)
                <option value="{{ $accion }}">{{ $datos[0] }}</option>
            @endforeach
        </select>
    </div>
    <div class="card-body table-responsive">
        <table id="accesosTable" class="table table-striped table-bordered table-sm">
            <thead class="table-dark">
                <tr>
                    <th>Fecha</th>
                    <th>Usuario</th>
                    <th>Acción</th>
                    <th>Detalle</th>
                    <th>IP</th>
                </tr>
            </thead>
            <tbody>
                @foreach($accesos as $acceso)
                <tr>
                    <td class="text-nowrap" data-order="{{ $acceso->created_at }}">{{ \Carbon\Carbon::parse($acceso->created_at)->format('d/m/Y H:i:s') }}</td>
                    <td>{{ $acceso->usuario?->name ?? '—' }}</td>
                    <td>
                        @php($info = $etiquetas[$acceso->accion] ?? [$acceso->accion, 'secondary'])
                        <span class="badge bg-{{ $info[1] }}">{{ $info[0] }}</span>
                    </td>
                    <td>{{ $detalles[$acceso->detalle] ?? $acceso->detalle ?? '—' }}</td>
                    <td class="text-nowrap" title="{{ $acceso->ip }}">
                        {{ $acceso->ip_bruta ?? (Str::length($acceso->ip) > 20 ? Str::limit($acceso->ip, 20) : $acceso->ip) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-footer d-flex justify-content-end">
        {{ $accesos->links() }}
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function () {
    const tabla = $('#accesosTable').DataTable({
        order: [[0, 'desc']],
        stateSave: true,
        language: { url: '{{ asset('js/datatables/i18n/es-ES.json') }}' }
    });

    $('#filtroAccion').on('change', function () {
        tabla.column(2).search(this.value).draw();
    });
});
</script>
@endsection
