<div class="card bg-dark bg-opacity-10 border-0">
    <div class="card-header d-flex justify-content-between align-items-center bg-dark bg-opacity-75 text-white border-bottom">
        <h5 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Gestor de Documentos</h5>
        <div>
            @if($esMapa)
                @if($pdfUrl)
                <a href="{{ $pdfUrl }}?download=1" class="btn btn-success btn-sm">
                    <i class="bi bi-download me-1"></i>Descargar PDF
                </a>
                @endif
            @else
                <a href="{{ route('sgiem.admin.cuadros.documento.exportar', $cuadro->cuadro_id) }}"
                   class="btn btn-success btn-sm">
                    <i class="bi bi-download me-1"></i>Descargar Excel
                </a>
                <a href="{{ route('sgiem.admin.cuadros.dataset', $cuadro->cuadro_id) }}"
                   class="btn btn-outline-info btn-sm ms-2">
                    <i class="bi bi-table me-1"></i>Dataset
                </a>
            @endif
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

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if($esMapa)
            {{-- == Mapa PDF == --}}
            <div class="alert alert-info d-flex align-items-center gap-2 py-2">
                <i class="bi bi-map"></i>
                <span>Este cuadro es de tipo <strong>Mapa PDF</strong>. Gestiona el archivo PDF aquí.</span>
            </div>

            @if($pdfUrl)
            <div class="mb-3">
                <div class="border rounded bg-white p-2" style="height:70vh;">
                    <object data="{{ $pdfUrl }}#toolbar=1" type="application/pdf" width="100%" height="100%">
                        <p>Tu navegador no puede mostrar el PDF. <a href="{{ $pdfUrl }}" target="_blank">Descárgalo aquí</a>.</p>
                    </object>
                </div>
            </div>
            @endif

            <div class="card border">
                <div class="card-body">
                    <form action="{{ route('sgiem.admin.cuadros.documento.upload-pdf', $cuadro->cuadro_id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="bi bi-filetype-pdf me-1"></i>
                                {{ $pdfUrl ? 'Actualizar PDF' : 'Cargar PDF' }}
                            </label>
                            <input type="file" class="form-control" name="pdf_file" accept=".pdf,application/pdf" required>
                            <small class="text-muted">Máximo 10 MB, solo PDF.</small>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-upload me-1"></i>{{ $pdfUrl ? 'Actualizar PDF' : 'Subir PDF' }}
                        </button>
                        @if($pdfUrl)
                        <small class="text-muted ms-3">Archivo actual: <code>{{ $cuadro->pdf_file }}</code></small>
                        @endif
                    </form>
                </div>
            </div>

        @else
            {{-- == Excel normal == --}}
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
        @endif
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