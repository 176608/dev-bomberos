@extends('layouts.biblioteca')  

@section('title', 'Catálogo Biblioteca')

{{-- ESTILOS: Ocultar buscador duplicado y ajustar móvil --}}
@push('styles')
<style>
    /*Ocultar el buscador redundante de DataTables */
    .dataTables_filter {
        display: none !important;
    }

    /* 📱 Ajustes para Móvil */
    @media (max-width: 768px) {
        /* Reduce el padding superior para que el contenido suba */
        .main-content {
            padding-top: 5px !important; 
        }
        /* Reduce el espacio entre las cajas (Stats, Clasificación, Buscador) */
        .mobile-gap {
            margin-bottom: 10px !important;
        }
    }
</style>
@endpush

@section('content')
    {{-- Stats: Solo si NO hay búsqueda activa --}}
    @if(empty($query))
        <div class="mobile-gap" style="margin-bottom: 20px;">
            @include('partialsbiblioteca.stats')
        </div>
    @endif

    {{-- Clasificación: Solo si NO hay búsqueda activa --}}
    @if(empty($query))
        <div class="mobile-gap" style="margin-bottom: 20px;">
            @include('partialsbiblioteca.material_stats')
        </div>
    @endif

    {{-- Buscador Principal (siempre visible) --}}
    <div class="mobile-gap" style="margin-bottom: {{ empty($query) ? '15px' : '10px' }}">
        @include('partialsbiblioteca.search_simple')
    </div>

    {{-- Resultados --}}
    @include('partialsbiblioteca.table')
@endsection

@push('scripts')
    @include('partialsbiblioteca.scripts')
@endpush