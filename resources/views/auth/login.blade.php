<!-- Archivo Bomberos - NO ELIMINAR COMENTARIO -->
@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <!-- Panel izquierdo: Acceso a Consultor -->
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100 panel-link" data-href="{{ url('/consultor') }}">
                <div class="card-body text-center">
                    <i class="bi bi-binoculars-fill display-4 text-primary mb-3"></i>
                    <h5 class="card-title">Acceso a Consultor</h5>
                    <p class="card-text">Visualiza la información de hidrantes sin necesidad de iniciar sesión.</p>
                    <button class="btn btn-outline-primary mt-2">
                        <i class="bi bi-arrow-right-circle"></i> Ir a Consultor
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Panel central: Login -->
        <div class="col-md-6">
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
        
        <!-- Panel derecho: Acceso a SIGEM -->
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100 panel-link" data-href="{{ url('/sigem') }}">
                <div class="card-body text-center">
                    <i class="bi bi-map-fill display-4 text-success mb-3"></i>
                    <h5 class="card-title">Acceso a SIGEM</h5>
                    <p class="card-text">Sistema de Información Geográfica y Estadística Municipal.</p>
                    <button class="btn btn-outline-success mt-2">
                        <i class="bi bi-arrow-right-circle"></i> Ir a SIGEM
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
$(function() {
    // Funcionalidad de los paneles laterales
    $('.panel-link').on('click', function() {
        const href = $(this).data('href');
        if (href) {
            window.location.href = href;
        }
    });

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

.panel-link {
    transition: all 0.3s ease;
    cursor: pointer;
}

.panel-link:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
}

.panel-link .btn {
    transition: all 0.3s ease;
}

.panel-link:hover .btn {
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
}

/* Estilos para los íconos de los paneles */
.panel-link i.display-4 {
    transition: all 0.3s ease;
}

.panel-link:hover i.display-4 {
    transform: scale(1.1);
}
</style>
@endsection