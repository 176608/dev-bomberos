@extends('layouts.biblioteca')  

@section('title', 'Búsqueda Avanzada | Catálogo Biblioteca')

@section('content')
@include('partialsbiblioteca.search_advanced')  

    <!-- Lista de resultados -->
    @include('partialsbiblioteca.table')
@endsection

@push('scripts')
    @include('partialsbiblioteca.scripts')
@endpush