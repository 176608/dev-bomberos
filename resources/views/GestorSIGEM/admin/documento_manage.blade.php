<div class="card bg-dark bg-opacity-10 border-0">
    <div class="card-header d-flex justify-content-between align-items-center bg-dark bg-opacity-75 text-white border-bottom">
        <h5 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Gestor de Documentos</h5>
        <div>
            <a href="{{ route('sgiem.admin.cuadros.documento.exportar', $cuadro->cuadro_id) }}"
               class="btn btn-success btn-sm">
                <i class="bi bi-download me-1"></i>Descargar Excel
            </a>
            <a href="{{ route('sgiem.admin.cuadros.dataset', $cuadro->cuadro_id) }}"
               class="btn btn-outline-info btn-sm ms-2">
                <i class="bi bi-table me-1"></i>Dataset
            </a>
            <a href="{{ route('sgiem.admin.cuadros.index') }}"
               class="btn btn-outline-light btn-sm ms-2">
                <i class="bi bi-arrow-left me-1"></i>Volver
            </a>
        </div>
    </div>
    <div class="card-body bg-transparent">
        <div class="row mb-4">
            <div class="col-md-8">
                <h4>{{ $cuadro->codigo_cuadro }} — {{ $cuadro->c_titulo }}</h4>
                @if($cuadro->c_subtitulo)
                    <p class="text-muted">{{ $cuadro->c_subtitulo }}</p>
                @endif
            </div>
            <div class="col-md-4 text-md-end">
                <span class="badge bg-{{ $cuadro->publicado ? 'success' : 'secondary' }} fs-6">
                    {{ $cuadro->publicado ? 'Publicado' : 'Restringido' }}
                </span>
                <small class="d-block text-muted mt-1">
                    Última actualización: {{ $cuadro->updated_at ? $cuadro->updated_at->format('d/m/Y H:i') : '—' }}
                </small>
            </div>
        </div>

        <div class="alert alert-info d-flex align-items-center gap-2 py-2">
            <i class="bi bi-info-circle"></i>
            <span>Esta es una <strong>previsualización</strong> del documento Excel tal como se genera. Para editar el pie de página, usa el modal de editar metadatos del cuadro.</span>
        </div>

        <div class="document-preview border rounded bg-white p-3 overflow-auto" style="max-height:75vh;">
            @include('exports.cuadro_excel', [
                'codigoCuadro' => $cuadro->codigo_cuadro,
                'tituloCuadro' => $cuadro->c_titulo,
                'subtituloCuadro' => $cuadro->c_subtitulo ?? '',
                'piePagina' => $cuadro->pie_pagina,
                'seccionesData' => $seccionesData,
                'mostrarLogo' => false,
            ])
        </div>
    </div>
</div>

<style>
.document-preview table {
    border-collapse: collapse;
    width: 100%;
}
.document-preview table th,
.document-preview table td {
    border: 1px solid #000;
    padding: 4px 8px;
    font-size: 10pt;
}
.document-preview table th {
    background: #e8edf2;
    font-weight: bold;
    text-align: center;
}
</style>