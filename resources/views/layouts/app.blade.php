<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Módulo Cartografía')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
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

    <!-- Reemplazada la sección de la barra de navegación -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top" style="background-color:rgb(102, 209, 147);">
        <div class="container-fluid">
            <img src="{{ asset('img/logo/IMIP_logo00.png') }}" alt="Logo imip" height="50" class="d-inline-block align-text-top">
            <a class="navbar-brand" href="https://www.imip.org.mx/">Cartografía</a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard') }}">Información Pública</a>
                    </li>
                    @auth
                        @if(auth()->user()->role === 'Administrador')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.panel') }}">Panel Administrador</a>
                            </li>
                        @elseif(auth()->user()->role === 'Desarrollador')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('dev.panel') }}">Panel Desarrollador</a>
                            </li>
                        @elseif(auth()->user()->role === 'Analista')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('analista.panel') }}">Panel Bomberos</a>
                            </li>
                        @endif
                    @endauth
                </ul>

                <ul class="navbar-nav">
                    @guest
                        <li class="nav-item d-flex align-items-center me-3">
                            <span class="nav-link">
                                <i class="bi bi-person-circle"></i> Invitado
                            </span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link login-btn" href="{{ route('login') }}">
                                <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
                            </a>
                        </li>
                    @else
                        <li class="nav-item d-flex align-items-center me-3">
                            <span class="nav-link">
                                <i class="bi bi-person-circle"></i> {{ Auth::user()->email }}
                            </span>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link btn btn-link logout-btn" 
                                    onclick="handleLogout(event)">
                                <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                            </button>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
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
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    @yield('scripts')

    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Login button click handler
    const loginBtn = document.querySelector('.login-btn');
    if (loginBtn) {
        loginBtn.addEventListener('click', function(e) {
            console.log('🔑 Iniciando proceso de login...');
        });
    }
});

// Logout handler function
function handleLogout(event) {
    event.preventDefault();
    console.log('🚪 Iniciando proceso de logout...');
    
    const form = document.getElementById('logout-form');
    if (form) {
        form.submit();
    } else {
        console.error('❌ Error: Formulario de logout no encontrado');
    }
}

// Add AJAX setup for CSRF token
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
</script>
</body>
</html>