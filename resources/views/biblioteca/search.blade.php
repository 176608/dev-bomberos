@extends('layouts.app')

@section('title', 'Búsqueda de Libros | Catálogo Biblioteca')

@section('content')
    @include('partialsbiblioteca.search_simple')

    <!-- Filtros de Materiales y Gráfica -->
    @include('partialsbiblioteca.material_stats')

    <!-- Lista de resultados -->
    @include('partialsbiblioteca.table')
@endsection

@push('scripts')
    @include('partialsbiblioteca.scripts')
@endpush