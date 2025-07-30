<div class="card shadow-sm">
    <div class="card-body">
        @if(isset($cuadro) && $cuadro)
            <!-- CUADRO ESPECÍFICO -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="text-success mb-0">
                    <i class="bi bi-table me-2"></i>{{ $cuadro->cuadro_estadistico_titulo }}
                </h2>
                <div>
                    <span class="badge bg-primary fs-6">{{ $cuadro->codigo_cuadro }}</span>
                </div>
            </div>

            @if($cuadro->cuadro_estadistico_subtitulo)
                <p class="text-muted mb-3">{{ $cuadro->cuadro_estadistico_subtitulo }}</p>
            @endif

            <!-- INFORMACIÓN DEL CUADRO -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-title">
                                <i class="bi bi-info-circle me-2"></i>Información General
                            </h6>
                            <p><strong>Tema:</strong> {{ $cuadro->subtema->tema->tema_titulo ?? 'N/A' }}</p>
                            <p><strong>Subtema:</strong> {{ $cuadro->subtema->subtema_titulo ?? 'N/A' }}</p>
                            <p><strong>Código:</strong> <code>{{ $cuadro->codigo_cuadro }}</code></p>
                            <p class="mb-0"><strong>ID:</strong> {{ $cuadro->cuadro_estadistico_id }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-title">
                                <i class="bi bi-file-earmark me-2"></i>Archivos Disponibles
                            </h6>
                            @if($cuadro->excel_file)
                                <p><i class="bi bi-file-excel text-success"></i> Excel: {{ $cuadro->excel_file }}</p>
                            @endif
                            @if($cuadro->pdf_file)
                                <p><i class="bi bi-file-pdf text-danger"></i> PDF: {{ $cuadro->pdf_file }}</p>
                            @endif
                            @if($cuadro->img_name)
                                <p><i class="bi bi-image text-info"></i> Imagen: {{ $cuadro->img_name }}</p>
                            @endif
                            @if($cuadro->permite_grafica)
                                <p><i class="bi bi-graph-up text-warning"></i> Permite gráficas</p>
                            @endif
                            @if(!$cuadro->excel_file && !$cuadro->pdf_file && !$cuadro->img_name)
                                <p class="text-muted">Sin archivos asociados</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- DATASET COMPLETO -->
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-database me-2"></i>Dataset Completo del Cuadro
                    </h5>
                </div>
                <div class="card-body">
                    <pre style="background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; max-height: 500px; font-size: 12px;">{{ json_encode($cuadro->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            </div>

            <!-- PIE DE PÁGINA -->
            @if($cuadro->pie_pagina)
                <div class="alert alert-light mt-3">
                    <small><strong>Nota:</strong> {{ $cuadro->pie_pagina }}</small>
                </div>
            @endif

        @else
            <!-- SIN PARÁMETROS -->
            <div class="text-center py-5">
                <i class="bi bi-exclamation-triangle text-warning" style="font-size: 3rem;"></i>
                <h3 class="mt-3 text-muted">Click sin parámetros</h3>
                <p class="text-muted">Selecciona un cuadro estadístico específico desde el catálogo para ver su información detallada.</p>
                
                <div class="mt-4">
                    <a href="javascript:window.close();" class="btn btn-outline-secondary me-2">
                        <i class="bi bi-x-circle me-1"></i>Cerrar Pestaña
                    </a>
                    <a href="{{ route('sigem.laravel.index') }}" target="_blank" class="btn btn-success">
                        <i class="bi bi-house me-1"></i>Ir a SIGEM
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>