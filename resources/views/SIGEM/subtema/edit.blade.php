@extends('layouts.app')

@section('title', 'Editar Subtema')

@section('content')
@auth
    @if(Auth::user()->name === 'admin')
        <div class="container mt-4">
            <h2 class="mb-4">Editar Subtema</h2>

            <form action="{{ route('subtema.update', $subtema->ce_subtema_id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="ce_subtema" class="form-label">Nombre del Subtema</label>
                    <input type="text" class="form-control" name="ce_subtema" id="ce_subtema" value="{{ $subtema->ce_subtema }}" required>
                </div>

                <div class="mb-3">
                    <label for="id_tema" class="form-label">Tema Relacionado</label>
                    <select class="form-control" name="id_tema" id="id_tema" required>
                        @foreach($temas as $tema)
                            <option value="{{ $tema->id }}" {{ $subtema->id_tema == $tema->id ? 'selected' : '' }}>
                                {{ $tema->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Actualizar</button>
                <a href="{{ route('subtema.index') }}" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    @else
        <div class="container mt-4">
            <div class="alert alert-warning text-center">
                No tienes permisos para acceder a esta sección.
            </div>
        </div>
    @endif
@endauth
@endsection
