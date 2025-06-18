@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center align-items-center">
        <div class="col-sm-6">
            <div class="card shadow-lg border-0" style="background-color: rgba(161, 224, 152, 0.8);">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <img src="{{ asset('img/logo/IMIP_logo00.png') }}" alt="Logo IMIP" height="80">
                        <h4 class="mt-3 mb-4">
                            {{ $user->log_in_status === 1 ? 'Completar Alta' : 'Cambiar Contraseña' }}
                        </h4>
                        @if(session('message'))
                            <div class="alert alert-info">
                                {{ session('message') }}
                            </div>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('password.reset.update') }}" class="needs-validation" novalidate>
                        @csrf

                        @if($user->log_in_status === 1)
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" placeholder="Nombre" required
                                    value="{{ old('name', $user->name) }}">
                                <label for="name">Nombre</label>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-floating mb-3">
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email" placeholder="Email" required
                                    value="{{ old('email', $user->email) }}">
                                <label for="email">Email</label>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        <div class="form-floating mb-3">
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                id="password" name="password" placeholder="Nueva Contraseña" required minlength="8">
                            <label for="password">Nueva Contraseña</label>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-floating mb-3">
                            <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" 
                                id="password_confirmation" name="password_confirmation" 
                                placeholder="Confirmar Contraseña" required minlength="8">
                            <label for="password_confirmation">Confirmar Contraseña</label>
                        </div>

                        <input type="hidden" name="email" value="{{ $user->email }}">

                        <button type="submit" class="btn btn-success w-100 py-2 mb-3">
                            <i class="bi bi-key"></i> 
                            {{ $user->log_in_status === 1 ? 'Completar Registro' : 'Actualizar Contraseña' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection