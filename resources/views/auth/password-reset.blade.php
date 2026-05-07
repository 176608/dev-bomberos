<!-- Archivo: password-reset.blade.php -->
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
                            {{-- Campos nombre y correo (sin cambios) --}}
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
                                <label for="email">Correo</label>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        {{-- Nueva Contraseña --}}
                        <div class="form-floating mb-3">
                            <div class="input-group">
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                    id="password" name="password" placeholder="Nueva Contraseña" required 
                                    minlength="12" title="La contraseña debe tener al menos 12 caracteres"
                                    style="border-top-right-radius: 0; border-bottom-right-radius: 0;">
                                <button class="btn btn-outline-secondary toggle-password" type="button" 
                                    data-target="password"
                                    style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                                    <i class="bi bi-eye-slash"></i>
                                </button>
                            </div>
                            <label for="password">Nueva Contraseña</label>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Confirmar Contraseña --}}
                        <div class="form-floating mb-3">
                            <div class="input-group">
                                <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" 
                                    id="password_confirmation" name="password_confirmation" 
                                    placeholder="Confirmar Contraseña" required minlength="12"
                                    title="La contraseña debe tener al menos 12 caracteres"
                                    style="border-top-right-radius: 0; border-bottom-right-radius: 0;">
                                <button class="btn btn-outline-secondary toggle-password" type="button" 
                                    data-target="password_confirmation"
                                    style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                                    <i class="bi bi-eye-slash"></i>
                                </button>
                            </div>
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

{{-- Script para mostrar/ocultar contraseñas solo mientras se presiona el botón --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Para cada botón con clase .toggle-password
        document.querySelectorAll('.toggle-password').forEach(button => {
            const targetId = button.getAttribute('data-target');
            const input = document.getElementById(targetId);
            if (!input) return;

            const icon = button.querySelector('i');

            const showPassword = () => {
                input.type = 'text';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            };

            const hidePassword = () => {
                input.type = 'password';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            };

            // Mientras se presiona el botón
            button.addEventListener('mousedown', showPassword);
            // Al soltar
            window.addEventListener('mouseup', (e) => {
                // Solo ocultamos si el mouseup no es sobre el mismo botón
                if (e.target !== button && !button.contains(e.target)) {
                    hidePassword();
                } else {
                    // Si soltó sobre el botón, también ocultamos
                    hidePassword();
                }
            });
            // Si el cursor sale del botón mientras está presionado
            button.addEventListener('mouseleave', hidePassword);

            // Soporte táctil (para móviles)
            button.addEventListener('touchstart', (e) => {
                e.preventDefault(); // evita el zoom o comportamientos no deseados
                showPassword();
            });
            button.addEventListener('touchend', hidePassword);
            button.addEventListener('touchcancel', hidePassword);
        });
    });
</script>
@endpush
@endsection