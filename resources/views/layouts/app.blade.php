<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Módulo Cartografía')</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
</head>
<body>
    <div class="bg-dark text-white py-1">
        <div class="container">
            <div class="d-flex justify-content-between">
                <div>Instituto Municipal de Investigación y Planeación</div>
                <div>Ciudad Juárez, Chihuahua</div>
            </div>
        </div>
    </div>

    <nav class="navbar navbar-expand-lg navbar-dark bg-danger fixed-top">
        <div class="container">
            <a class="navbar-brand" href="https://www.imip.org.mx/">
                <img src="{{ asset('images/logo/IMIP_logo00.png') }}" 
                     alt="Logo imip" height="40" class="d-inline-block align-text-top">
                Cartografía
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard') }}">Hidrantes</a>
                    </li>
                    @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('aux') }}">Vista Auxiliar</a>
                        </li>
                        <!-- Más items del menú protegidos -->
                    @endauth
                </ul>
                
                <ul class="navbar-nav">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Iniciar Sesión</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">Registrarse</a>
                        </li>
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#">Rol: {{ Auth::user()->role }}</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Cerrar Sesión</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <main class="py-4">
        <div class="container-fluid">
            @yield('content')
        </div>
    </main>

    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <img src="{{ asset('images/logo/IMIP_logo01.png') }}" alt="Logo imip1" height="40" class="d-inline-block align-text-top">
                </div>
                <div class="col-md-6 text-md-end">
                    <img src="{{ asset('images/logo/HCJ_logo00.png') }}" alt="Logo HCJ" height="40" class="d-inline-block align-text-top">
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts en orden correcto -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    
    @yield('scripts')
</body>
</html>