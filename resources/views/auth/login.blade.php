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

                        <form method="POST" action="{{ route('login') }}" class="needs-validation" novalidate>
                            @csrf

                            <div class="form-floating mb-3">
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                    id="email" name="email" value="{{ old('email') }}" 
                                    placeholder="nombre@ejemplo.com" required>
                                <label for="email">Correo Electrónico:</label>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-floating mb-3">
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                    id="password" name="password" placeholder="Contraseña" required>
                                <label for="password">Contraseña:</label>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-success w-100 py-2 mb-3">
                                <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
                            </button>

                            @if($errors->any())
                                <div class="alert alert-danger">
                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                    Credenciales incorrectas
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
    .card {
        backdrop-filter: blur(10px);
        background-color: rgba(255, 255, 255, 0.9);
        border-radius: 15px;
    }

    .form-floating > .form-control:focus,
    .form-floating > .form-control:not(:placeholder-shown) {
        padding-top: 1.625rem;
        padding-bottom: 0.625rem;
    }

    .btn-primary:hover {
        background-color: rgb(82, 169, 117);
        border-color: rgb(72, 149, 102);
    }
    </style>
    @endsection