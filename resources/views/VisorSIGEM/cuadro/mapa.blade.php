@extends('VisorSIGEM.layouts.visor')

@section('visor_title', 'Mapa — ' . ($cuadro->codigo_cuadro ?? ''))

@section('visor_content')
<div class="container-fluid py-3">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="mb-0"><i class="bi bi-map-fill me-2"></i>Mapa</h5>
            <small class="text-muted">
                <code>{{ $cuadro->codigo_cuadro }}</code>
                <strong>{{ $cuadro->c_titulo }}</strong>
            </small>
        </div>
        <div class="d-flex gap-2">
            @if($cuadro->pdf_file)
            <a href="{{ route('sigem.v2.cuadro.mapa.descargar', $cuadro->cuadro_id) }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-download me-1"></i> Descargar PDF
            </a>
            @endif
        </div>
    </div>

    @if($cuadro->pdf_file)
    <div class="mb-3">
        <iframe src="{{ asset('u_pdf/' . $cuadro->pdf_file) }}#view=FitW" type="application/pdf" style="width:100%;min-height:90vh;border:1px solid #dee2e6;border-radius:4px;">
            <p class="text-muted py-5 text-center">
                <i class="bi bi-filetype-pdf me-2" style="font-size:2rem;"></i><br>
                El navegador no puede mostrar el PDF. 
                <a href="{{ route('sigem.v2.cuadro.mapa.descargar', $cuadro->cuadro_id) }}" class="btn btn-sm btn-outline-primary mt-2">
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
