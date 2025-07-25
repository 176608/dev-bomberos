<!-- Archivo Bomberos - NO ELIMINAR COMENTARIO -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Módulo Cartografía')</title>

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    
    <!-- Scripts base -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.70/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.70/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
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
                        @if(auth()->user()->log_in_status === 0)
                            {{-- CAMBIO: Desarrollador ve todas las opciones --}}
                            @if(auth()->user()->role === 'Desarrollador')
                                {{-- Panel Desarrollador --}}
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('dev.panel') }}">
                                        <i class="bi bi-code-slash"></i> Panel Desarrollador
                                    </a>
                                </li>
                                {{-- Panel Admin (acceso desarrollador) --}}
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('admin.panel') }}">
                                        <i class="bi bi-gear"></i> Panel Administrador
                                    </a>
                                </li>
                                {{-- Panel Capturista (acceso desarrollador) --}}
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('capturista.panel') }}">
                                        <i class="bi bi-fire"></i> Panel Bomberos
                                    </a>
                                </li>
                                {{-- Indicador visual de modo desarrollador --}}
                                <li class="nav-item">
                                    <span class="nav-link text-warning">
                                        <i class="bi bi-tools"></i> DEV MODE
                                    </span>
                                </li>
                            @else
                                {{-- Menú normal para otros roles --}}
                                @if(auth()->user()->role === 'Administrador')
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('admin.panel') }}">Panel Administrador</a>
                                    </li>
                                @elseif(auth()->user()->role === 'Capturista') 
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('capturista.panel') }}">Panel Bomberos</a>
                                    </li>
                                @endif
                            @endif
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

    <!-- Scripts en orden -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.70/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.70/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    
    @yield('scripts')

    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Login button click handler
    const loginBtn = document.querySelector('.login-btn');
    if (loginBtn) {
        loginBtn.addEventListener('click', function(e) {
            console.log('Iniciando proceso de login...');
        });
    }
});

// Logout handler function
function handleLogout(event) {
    event.preventDefault();
    console.log('Iniciando proceso de logout...');
    
    const form = document.getElementById('logout-form');
    if (form) {
        form.submit();
    } else {
        console.error('Error: Formulario de logout no encontrado');
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