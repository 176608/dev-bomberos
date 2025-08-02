<!-- SIGEM PUNTO BLADE PUNTO PHP -->
@extends('layouts.asigem')

@section('dynamic_content')
    <!-- Contenido inicial por defecto (se carga por JavaScript) -->
    <div class="Cargando">
        <i class="bi bi-hourglass-split"></i>
        <p>Cargando contenido...</p>
    </div>
@endsection

{{-- Asegúrate de que esta parte exista en la vista principal --}}
@if(isset($section) && $section === 'estadistica')
    @include('partials.estadistica')
@endif