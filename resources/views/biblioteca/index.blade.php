@extends('layouts.biblioteca')  

@section('title', 'Catálogo Biblioteca')

@section('content')
    <!-- Vista Pública (Simple Search) -->
    @include('partialsbiblioteca.stats')
    @include('partialsbiblioteca.search_simple')
    @include('partialsbiblioteca.material_stats')
    @include('partialsbiblioteca.table')
@endsection

@push('scripts')
    @include('partialsbiblioteca.scripts')
@endpush