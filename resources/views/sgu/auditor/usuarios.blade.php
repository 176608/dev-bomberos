@extends('sgu.layouts.admin')

@section('title', 'SGU v2 — Auditor: Cambios en usuarios')

@section('sgu_content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h4 class="mb-0"><i class="bi bi-shield-lock-fill"></i> Auditor — Cambios en usuarios</h4>
    <div class="btn-group" role="group">
        <a href="{{ route('sgu.admin.auditor.accesos') }}"
           class="btn btn-sm btn-outline-danger {{ request()->routeIs('sgu.admin.auditor.accesos') ? 'active' : '' }}">
            <i class="bi bi-box-arrow-in-right"></i> Accesos
        </a>
        <a href="{{ route('sgu.admin.auditor.usuarios') }}"
           class="btn btn-sm btn-danger {{ request()->routeIs('sgu.admin.auditor.usuarios') ? 'active' : '' }}">
            <i class="bi bi-pencil-square"></i> Cambios en usuarios
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Historial de cambios en usuarios</span>
        <select id="filtroAccion" class="form-select form-select-sm" style="width:auto;">
            <option value="">Todas las acciones</option>
            <option value="crear">Crear</option>
            <option value="actualizar">Actualizar</option>
            <option value="eliminar">Eliminar</option>
        </select>
    </div>
    <div class="card-body table-responsive">
        <table id="auditoriaTable" class="table table-striped table-bordered table-sm">
            <thead class="table-dark">
                <tr>
                    <th>Fecha</th>
                    <th>Actor</th>
                    <th>Usuario afectado</th>
                    <th>Acción</th>
                    <th>Detalles</th>
                </tr>
            </thead>
            <tbody>
                @foreach($auditorias as $auditoria)
                <tr>
                    <td class="text-nowrap">{{ $auditoria->created_at->format('d/m/Y H:i:s') }}</td>
                    <td>{{ $auditoria->actor?->name ?? '—' }}</td>
                    <td>{{ $auditoria->usuario?->name ?? ('ID ' . $auditoria->usuario_id) }}</td>
                    <td>
                        @php($badge = ['crear' => 'success', 'actualizar' => 'warning', 'eliminar' => 'danger'])
                        <span class="badge bg-{{ $badge[$auditoria->accion] ?? 'secondary' }}">
                            {{ ucfirst($auditoria->accion) }}
                        </span>
                    </td>
                    <td>
                        @if ($auditoria->accion === 'actualizar')
                            <button type="button" class="btn btn-sm btn-outline-primary" id="btnCambios{{ $auditoria->auditoria_id }}"
                                    data-previos='@json($auditoria->datos_previos)' data-nuevos='@json($auditoria->datos_nuevos)'
                                    data-bs-toggle="modal" data-bs-target="#modalCambios"
                                    onclick="verCambios({{ $auditoria->auditoria_id }})">
                                Ver cambios
                            </button>
                        @elseif ($auditoria->accion === 'crear')
                            <button type="button" class="btn btn-sm btn-outline-success" id="btnCrear{{ $auditoria->auditoria_id }}"
                                    data-previos='@json($auditoria->datos_previos)' data-nuevos='@json($auditoria->datos_nuevos)'
                                    data-bs-toggle="modal" data-bs-target="#modalCambios"
                                    onclick="verCambios({{ $auditoria->auditoria_id }})">
                                Ver registro
                            </button>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-footer d-flex justify-content-end">
        {{ $auditorias->links() }}
    </div>
</div>

<div class="modal fade" id="modalCambios" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cambios registrados</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="table-dark">
                            <tr>
                                <th style="width:30%;">Campo</th>
                                <th style="width:35%;">Antes</th>
                                <th style="width:35%;">Después</th>
                            </tr>
                        </thead>
                        <tbody id="cambiosBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function verCambios(id) {
    const btn = document.getElementById('btnCambios' + id) || document.getElementById('btnCrear' + id);
    const esc = function (s) {
        return String(s)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    };
    let previos = {};
    let nuevos = {};
    try { previos = JSON.parse(btn.dataset.previos || '{}'); } catch (e) {}
    try { nuevos = JSON.parse(btn.dataset.nuevos || '{}'); } catch (e) {}

    const cuerpo = document.getElementById('cambiosBody');
    cuerpo.innerHTML = '';

    const claves = Array.from(new Set(Object.keys(previos).concat(Object.keys(nuevos))));
    if (!claves.length) {
        cuerpo.innerHTML = '<tr><td colspan="3" class="text-center text-muted">Sin campos registrados</td></tr>';
        return;
    }

    claves.forEach(function (clave) {
        const formato = function (valor) {
            if (valor === null || valor === undefined || valor === '') return '—';
            return (typeof valor === 'object') ? JSON.stringify(valor) : String(valor);
        };
        cuerpo.innerHTML += '<tr>'
            + '<td><code>' + esc(clave) + '</code></td>'
            + '<td>' + esc(formato(previos[clave])) + '</td>'
            + '<td>' + esc(formato(nuevos[clave])) + '</td>'
            + '</tr>';
    });
}
</script>

<script>
$(document).ready(function () {
    const tabla = $('#auditoriaTable').DataTable({
        order: [[0, 'desc']],
        language: { url: '{{ asset('js/datatables/i18n/es-ES.json') }}' }
    });

    $('#filtroAccion').on('change', function () {
        tabla.column(3).search(this.value).draw();
    });
});
</script>
@endsection
