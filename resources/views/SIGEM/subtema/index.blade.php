@extends('layouts.app')

@section('title', 'Listado de Subtemas')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Listado de Subtemas</h2>

    @auth
        @if(Auth::user()->name === 'admin')
            <a href="{{ route('subtema.create') }}" class="btn btn-success mb-3">
                <i class="fas fa-plus"></i> Nuevo Subtema
            </a>
        @endif
    @endauth

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-striped">
        <thead class="table-success">
            <tr>
                <th>ID</th>
                <th>Nombre del Subtema</th>
                <th>Tema Relacionado</th>
                @auth
                    @if(Auth::user()->name === 'admin')
                        <th>Acciones</th>
                    @endif
                @endauth
            </tr>
        </thead>
        <tbody>
            @foreach($subtemas as $subtema)
            <tr>
                <td>{{ $subtema->ce_subtema_id }}</td>
                <td>{{ $subtema->ce_subtema }}</td>
                <td>{{ $subtema->nombre_tema ?? 'Sin tema' }}</td>

                @auth
                    @if(Auth::user()->name === 'admin')
                        <td>
                            <a href="{{ route('subtema.edit', $subtema->ce_subtema_id) }}" class="btn btn-warning btn-sm">Editar</a>
                            <form action="{{ route('subtema.destroy', $subtema->ce_subtema_id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar subtema?')">Eliminar</button>
                            </form>
                        </td>
                    @endif
                @endauth
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
