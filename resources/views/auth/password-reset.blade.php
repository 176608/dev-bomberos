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
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" placeholder="Nombre" required
                                    value="{{ old('name', $user->name) }}">
                                <label for="name">Nombre</label>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Correo:</label>
                                <input type="text" class="form-control" value="{{ $user->email }}" readonly>
                                <small class="text-muted">El correo no puede ser modificado. Contacta al administrador para cambios.</small>
                            </div>
                        @endif

                        @if($requirePin || $user->initial_token)
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control @error('pin') is-invalid @enderror"
                                    id="pin" name="pin" placeholder="PIN de Acceso" required
                                    maxlength="10" pattern="\d{10}" inputmode="numeric">
                                <label for="pin">PIN de Acceso (10 dígitos)</label>
                                @error('pin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">El PIN fue proporcionado por el administrador</small>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="password" class="form-label">Nueva Contraseña:</label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('password') is-invalid @endif" 
                                    id="password" name="password" placeholder="Nueva Contraseña" required 
                                    minlength="12" title="La contraseña debe tener al menos 12 caracteres">
                                <button class="btn btn-outline-secondary toggle-password" type="button" 
                                    data-target="password">
                                    <i class="bi bi-eye-slash"></i>
                                </button>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @endif
                            </div>
                            <ul class="list-unstyled small mt-2 mb-1" id="pw-checklist">
                                <li id="chk-length"><i class="bi bi-x-circle text-danger"></i> Mínimo 12 caracteres</li>
                                <li id="chk-minus"><i class="bi bi-x-circle text-danger"></i> Una letra minúscula</li>
                                <li id="chk-mayus"><i class="bi bi-x-circle text-danger"></i> Una letra mayúscula</li>
                                <li id="chk-num"><i class="bi bi-x-circle text-danger"></i> Un número</li>
                                <li id="chk-sym"><i class="bi bi-x-circle text-danger"></i> Un símbolo</li>
                            </ul>
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirmar Contraseña:</label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('password_confirmation') is-invalid @endif" 
                                    id="password_confirmation" name="password_confirmation" 
                                    placeholder="Confirmar Contraseña" required minlength="12"
                                    title="La contraseña debe tener al menos 12 caracteres">
                                <button class="btn btn-outline-secondary toggle-password" type="button" 
                                    data-target="password_confirmation">
                                    <i class="bi bi-eye-slash"></i>
                                </button>
                            </div>
                            <small id="pw-confirm-msg" class="d-none"></small>
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.toggle-password').forEach(function (button) {
            const targetId = button.getAttribute('data-target');
            const input = document.getElementById(targetId);
            if (!input) return;

            const icon = button.querySelector('i');

            const showPassword = function () {
                input.type = 'text';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            };

            const hidePassword = function () {
                input.type = 'password';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            };

            button.addEventListener('mousedown', showPassword);
            window.addEventListener('mouseup', function () {
                hidePassword();
            });
            button.addEventListener('mouseleave', hidePassword);

            button.addEventListener('touchstart', function (e) {
                e.preventDefault();
                showPassword();
            });
            button.addEventListener('touchend', hidePassword);
            button.addEventListener('touchcancel', hidePassword);
        });

        const pinInput = document.getElementById('pin');
        if (pinInput) {
            pinInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/\D/g, '').slice(0, 10);
            });
        }

        const reglas = [
            { id: 'chk-length', test: v => v.length >= 12 },
            { id: 'chk-minus', test: v => /[a-z]/.test(v) },
            { id: 'chk-mayus', test: v => /[A-Z]/.test(v) },
            { id: 'chk-num', test: v => /[0-9]/.test(v) },
            { id: 'chk-sym', test: v => /[^A-Za-z0-9]/.test(v) },
        ];

        const passwordInput = document.getElementById('password');
        const confirmInput = document.getElementById('password_confirmation');
        const confirmMsg = document.getElementById('pw-confirm-msg');
        const submitBtn = document.querySelector('button[type="submit"]');

        function evaluar() {
            const v = passwordInput.value;
            let todasOk = true;
            reglas.forEach(r => {
                const ok = r.test(v);
                if (!ok) todasOk = false;
                const icono = document.querySelector('#' + r.id + ' i');
                icono.className = ok ? 'bi bi-check-circle-fill text-success' : 'bi bi-x-circle text-danger';
            });
            return todasOk;
        }

        function evaluarConfirmacion() {
            if (!confirmInput.value) {
                confirmMsg.classList.add('d-none');
                return false;
            }
            confirmMsg.classList.remove('d-none');
            const coincide = confirmInput.value === passwordInput.value;
            confirmMsg.textContent = coincide
                ? 'Las contraseñas coinciden.'
                : 'Las contraseñas no coinciden.';
            confirmMsg.className = coincide
                ? 'small text-success'
                : 'small text-danger';
            return coincide;
        }

        passwordInput.addEventListener('input', function () {
            evaluar();
            evaluarConfirmacion();
        });

        confirmInput.addEventListener('input', evaluarConfirmacion);

        submitBtn.addEventListener('click', function (e) {
            if (!evaluar() || !evaluarConfirmacion()) {
                e.preventDefault();
                return;
            }
        });
    });
</script>
@endsection