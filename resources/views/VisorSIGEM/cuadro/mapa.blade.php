@extends('VisorSIGEM.layouts.visor')

@section('visor_title', 'M-' . ($cuadro->codigo_cuadro ?? ''))

@section('visor_content')
<div class="container-fluid py-3">

    <div class="row align-items-center g-2 mb-3">
        <div class="col-12 col-md-8">
            @include('VisorSIGEM.partials.breadcrumb_cuadro', ['cuadro' => $cuadro])
            <div class="d-flex align-items-start gap-2">
                <i class="bi bi-map-fill fs-4 text-success lh-1"></i>
                <div>
                    <h5 class="mb-0">{{ $cuadro->c_titulo }}</h5>
                    @if($cuadro->c_subtitulo)
                        <div class="text-muted fst-italic" style="font-size:0.85rem">{{ $cuadro->c_subtitulo }}</div>
                    @endif
                    @if(!$cuadro->publicado)
                        <span class="badge bg-warning text-dark mt-1"><i class="bi bi-eye-slash me-1"></i>No publicado — vista previa</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4 d-flex justify-content-md-end align-items-center">
            @if($cuadro->pdf_file)
            <a href="{{ route('sigem.v2.cuadro.mapa.descargar', $cuadro->cuadro_id) }}" class="btn btn-outline-danger" title="Descargar el documento PDF de este mapa">
                <i class="bi bi-download me-1"></i> Descargar PDF
            </a>
            @endif
        </div>
    </div>

    @if($cuadro->pdf_file)
    <div class="mb-3" style="height:calc(100vh - 120px);">
        <iframe src="{{ route('sigem.v2.cuadro.mapa.ver', $cuadro->cuadro_id) }}" type="application/pdf" style="width:100%;height:100%;border:1px solid #dee2e6;border-radius:4px;">
            <p class="text-muted py-5 text-center">
                <i class="bi bi-filetype-pdf me-2" style="font-size:2rem;"></i><br>
                El navegador no puede mostrar el PDF. 
                <a href="{{ route('sigem.v2.cuadro.mapa.descargar', $cuadro->cuadro_id) }}" class="btn btn-sm btn-outline-danger mt-2">
                    <i class="bi bi-download me-1"></i>Descargar PDF
                </a>
            </p>
        </iframe>
    </div>
    @else
    <div class="text-center py-5 text-muted">
        <i class="bi bi-file-earmark-pdf" style="font-size:3rem;"></i>
        <p class="mt-3">Este mapa no tiene archivo PDF cargado.</p>
    </div>
    @endif

</div>
@endsection
