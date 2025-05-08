@extends('layouts.app')

@section('title', 'Vista Auxiliar')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Vista Auxiliar</h2>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="auxTable" class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <!-- Aquí irán los encabezados de tu tabla -->
                    </tr>
                </thead>
                <tbody>
                    <!-- Aquí irá el contenido de tu tabla -->
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection