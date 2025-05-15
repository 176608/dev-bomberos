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

    <nav class="navbar navbar-expand-lg navbar-light fixed-top" style="background-color:rgb(102, 209, 147);">
        <div class="container-fluid">
        <img src="{{ asset('img/logo/IMIP_logo00.png') }}" alt="Logo imip" height="50" class="d-inline-block align-text-top">
            <a class="navbar-brand" href="https://www.imip.org.mx/"> - Cartografía</a>
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

    <main class="py-4 mt-4">
        <div class="container-fluid">
            @yield('content')
        </div>
    </main>

    <footer class="bg-light text-dark py-4 mt-2">
        <div class="container">
            <div class="row">
                <div class="col-md-4 text-md-start">
                        <img src="{{ asset('img/logo/IMIP_logo01.png') }}" alt="Logo IMIP footer" height="100" class="d-inline-block align-text-top">
                    </div>
                    <div class="col-md-4">
                        <p class="mb-0">Calle Benjamín Franklin #4185</p>
                        <p class="mb-0">Colonia Progresista</p>
                        <p class="mb-0">C.P. 32310</p>
                        <p class="mb-0">Ciudad Juárez, Chihuahua, México</p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <img src="{{ asset('img/logo/HCJ_logo00.png') }}" alt="Logo ciudad juarez, footer" height="100" class="d-inline-block align-text-top">
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