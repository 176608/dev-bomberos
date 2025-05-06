@extends('layouts.app')

@section('title', 'Dashboard - Bomberos')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Dashboard</h2>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-body text-center">
                        <h5 class="card-title">Emergencias Activas</h5>
                        <p class="display-4">5</p>
                    </div>
                </div>
            </div>
            <!-- Agrega más elementos según necesites -->
        </div>
    </div>
</div>
@endsection