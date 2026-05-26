@extends('layouts.biblioteca')  

@section('title', 'Búsqueda Avanzada | Catálogo Biblioteca')

{{-- Ocultar el buscador redundante de DataTables --}}
@push('styles')
<style>
    .dataTables_filter {
        display: none !important;
    }
</style>
@endpush

@section('content')
    @include('partialsbiblioteca.search_advanced')  

    <!-- Lista de resultados -->
    @include('partialsbiblioteca.table')
@endsection

@push('scripts')
    @include('partialsbiblioteca.scripts')
@endpush