@extends('layouts.app')

@section('title', 'Galería de Imágenes')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4 text-center">Galería de Imágenes</h2>

    <div class="row">
        @foreach($imagenes as $img)
            <div class="col-md-3 mb-4 text-center">
                <img src="{{ asset($img) }}" alt="{{ basename($img) }}" class="img-fluid" style="max-height: 200px;">
                <p class="mt-2">{{ basename($img) }}</p>
            </div>
        @endforeach
    </div>
</div>
@endsection
