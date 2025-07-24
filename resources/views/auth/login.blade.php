<!-- Archivo Bomberos - NO ELIMINAR COMENTARIO -->
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center align-items-center">
        <div class="col-sm-6">
            <div class="card shadow-lg border-0" style="background-color: rgba(161, 224, 152, 0.8);">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <img src="{{ asset('img/logo/IMIP_logo00.png') }}" alt="Logo IMIP" height="80">
                        <h4 class="mt-3 mb-4">Iniciar Sesión</h4>
                    </div>

                    <form id="login-step1" class="needs-validation" novalidate>
                        @csrf
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" id="email" name="email"
                                placeholder="nombre@ejemplo.com" required autofocus>
                            <label for="email">Correo Electrónico:</label>
                        </div>
                        <button type="submit" class="btn btn-success w-100 py-2 mb-3">
                            <i class="bi bi-arrow-right"></i> Siguiente
                        </button>
                        <div id="login-error" class="alert alert-danger d-none mt-2"></div>
                    </form>

                    <form id="login-step2" method="POST" action="{{ route('login') }}" class="needs-validation d-none" novalidate>
                        @csrf
                        <input type="hidden" name="email" id="email-hidden">
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="password" name="password"
                                placeholder="Contraseña" required>
                            <label for="password">Contraseña:</label>
                        </div>
                        <button type="submit" class="btn btn-success w-100 py-2 mb-3">
                            <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
                        </button>
                        <div id="login-error2" class="alert alert-danger d-none mt-2"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
$(function() {
    $('#login-step1').on('submit', function(e) {
        e.preventDefault();
        let email = $('#email').val();
        $('#login-error').addClass('d-none').text('');
        $.post("{{ route('login.checkEmail') }}", {
            _token: '{{ csrf_token() }}',
            email: email
        }, function(response) {
            if (!response.exists) {
                $('#login-error').removeClass('d-none').text('Correo no registrado.');
                return;
            }
            if (response.log_in_status == 0) {
                // Mostrar campo de contraseña
                $('#login-step1').addClass('d-none');
                $('#login-step2').removeClass('d-none');
                $('#email-hidden').val(email);
                $('#password').focus();
            } else if (response.log_in_status == 1 || response.log_in_status == 2) {
                // Redirigir a password reset
                window.location.href = "{{ route('password.reset.form') }}?email=" + encodeURIComponent(email);
            }
        }).fail(function(xhr) {
            $('#login-error').removeClass('d-none').text('Error al verificar el correo.');
        });
    });

    // Manejo de errores en el segundo paso
    $('#login-step2').on('submit', function(e) {
        // Permite el submit normal, Laravel manejará el error si la contraseña es incorrecta
    });

    // Establecer el paso de inicio de sesión y el error en caso de que existan
    @if ($errors->has('password'))
        window.loginStep = 2;
        window.loginError = @json($errors->first('password'));
        window.loginEmail = @json(old('email'));
    @endif
    @if ($errors->has('email'))
        window.loginStep = 1;
        window.loginError = @json($errors->first('email'));
        window.loginEmail = @json(old('email'));
    @endif

    // Lógica para mostrar el paso correcto basado en los errores
    if (window.loginStep === 2) {
        $('#login-step1').addClass('d-none');
        $('#login-step2').removeClass('d-none');
        $('#email-hidden').val(window.loginEmail || '');
        $('#password').focus();
        $('#login-error2').removeClass('d-none').text(window.loginError || '');
    } else if (window.loginStep === 1) {
        $('#login-step1').removeClass('d-none');
        $('#login-step2').addClass('d-none');
        $('#email').val(window.loginEmail || '');
        $('#login-error').removeClass('d-none').text(window.loginError || '');
    }

    $('#password').on('input', function() {
        $('#login-error2').addClass('d-none').text('');
    });
});
</script>
@endsection

<style>
.card {
    backdrop-filter: blur(10px);
    background-color: rgba(255, 255, 255, 0.9);
    border-radius: 15px;
}
</style>
@endsection