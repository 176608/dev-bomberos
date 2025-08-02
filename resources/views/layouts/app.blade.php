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

<style>
    .top-bar {
        background-color: #2a6e48;
        color: white;
        padding: 8px 0;
        font-size: 14px;
    }
    
    .top-bar a {
        color: white !important;
        text-decoration: none !important;
    }
    
    .top-bar .left-section {
        margin-right: auto;
    }
    
    .top-bar .right-section {
        margin-left: auto;
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .top-bar .user-icon {
        width: 28px;
        height: 28px;
        background-color: white;
        color: #2a6e48;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* EFECTOS NAVBAR */
    .navbar .nav-link {
        position: relative;
        transition: all 0.3s ease;
        border-radius: 6px;
        margin: 0 2px;
        font-weight: 500;
    }

    /* Efecto HOVER */
    .navbar .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.1);
        color: #2a6e48 !important;
        background-color: rgba(255, 255, 255, 0.9);
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    /* Estado ACTIVE */
    .navbar .nav-link.active {
        background-color: #2a6e48;
        color: white !important;
        font-weight: bold;
        box-shadow: 0 2px 6px rgba(42, 110, 72, 0.3);
    }

    .navbar .nav-link.active:hover {
        background-color: #1e4d35;
        color: white !important;
        transform: none;
    }

    /* Indicador visual para active */
    .navbar .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 50%;
        transform: translateX(-50%);
        width: 80%;
        height: 3px;
        background-color: #ffd700;
        border-radius: 2px;
    }

    /* Modo desarrollador destacado */
    .navbar-text.text-danger {
        background-color: rgba(220, 53, 69, 0.1);
        padding: 4px 8px;
        border-radius: 4px;
        border: 1px solid #dc3545;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.7; }
        100% { opacity: 1; }
    }
</style>

<body>
    <!-- TOP BAR: Información institucional + sesión -->
    <div class="top-bar text-white">
        <div class="container-fluid"> {{-- CAMBIO: usar container-fluid en lugar de container --}}
            <div class="d-flex justify-content-between align-items-center w-100"> {{-- AGREGAR: w-100 --}}
                <!-- Información institucional (IZQUIERDA) -->
                <div class="left-section">
                    <a href="https://www.imip.org.mx/" class="text-white text-decoration-none">
                       Instituto Municipal de Investigación y Planeación
                    </a>
                    <span class="ms-2">| Ciudad Juárez, Chihuahua</span>
                </div>
                
                <!-- Información de sesión (DERECHA) -->
                <div class="d-flex align-items-center">
                    @guest
                        <span class="me-3">
                            <i class="bi bi-person-circle"></i> Invitado
                        </span>
                        <a href="{{ route('login') }}" class="text-white text-decoration-none">
                            <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
                        </a>
                    @else
                        <span class="me-3">
                            <i class="bi bi-person-circle"></i> {{ Auth::user()->email }}
                        </span>
                        <a type="button" class="text-white text-decoration-none" 
                                onclick="handleLogout(event)">
                            <i class="bi bi-box-arrow-left"></i> Cerrar Sesión
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    @endguest
                </div>
            </div>
        </div>
    </div>

    <!-- NAVBAR: Navegación principal del sistema -->
    @auth
        <nav class="navbar navbar-expand-lg navbar-light" style="background-color: rgb(102, 209, 147);">
            <div class="container-fluid">
                
                <!-- Toggle para móviles -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Navegación principal -->
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <!-- Información Pública (siempre visible SOLO para usuarios autenticados) -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('consultor.dashboard') ? 'active' : '' }}" 
                               href="{{ route('consultor.dashboard') }}" title="Panel de Consulta de hidrantes">
                                <i class="bi bi-binoculars-fill"></i> Consultor
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('sigem.index') ? 'active' : '' }}" 
                               href="{{ route('sigem.index') }}" title="Panel de consulta público de SIGEM">
                                <i class="bi bi-binoculars-fill"></i> SIGEM
                            </a>
                        </li>
                        
                        <!-- Paneles según rol (solo si está logueado y activo) -->
                        @if(auth()->user()->log_in_status === 0)
                            @if(auth()->user()->role === 'Desarrollador')
                                {{-- Desarrollador: 4 pestañas --}}
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('dev.panel') ? 'active' : '' }}" 
                                       href="{{ route('dev.panel') }}" title="Panel de Desarrollador">
                                        <i class="bi bi-code-slash"></i> DEV
                                    </a>
                                </li>
                                
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.panel') ? 'active' : '' }}" 
                                       href="{{ route('admin.panel') }}" title="Panel de Administración de Bomberos">
                                        <i class="bi bi-gear"></i> Usuarios
                                    </a>
                                </li>
                                
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('sigem.admin.index') ? 'active' : '' }}" 
                                       href="{{ route('sigem.admin.index') }}" title="Panel de Administración de SIGEM">
                                        <i class="bi bi-gear"></i> SIGEM ADMIN
                                    </a>
                                </li>
                                
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('capturista.panel') ? 'active' : '' }}" 
                                       href="{{ route('capturista.panel') }}" title="Panel de Captura de Hidrantes">
                                        <i class="bi bi-droplet-fill"></i> Hidrantes
                                    </a>
                                </li>
                                
                            @elseif(auth()->user()->role === 'Administrador')
                                {{-- Administrador: solo su panel --}}
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.panel') ? 'active' : '' }}" 
                                       href="{{ route('admin.panel') }}" title="Panel de Administración de Bomberos">
                                        <i class="bi bi-gear"></i> Usuarios
                                    </a>
                                </li>
                                
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('sigem.admin.index') ? 'active' : '' }}" 
                                       href="{{ route('sigem.admin.index') }}" title="Panel de Administración de SIGEM">
                                        <i class="bi bi-gear"></i> SIGEM ADMIN
                                    </a>
                                </li>
                                
                            @elseif(auth()->user()->role === 'Capturista') 
                                {{-- Capturista: solo su panel --}}
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('capturista.panel') ? 'active' : '' }}" 
                                       href="{{ route('capturista.panel') }}" title="Panel de Captura de Hidrantes">
                                        <i class="bi bi-droplet-fill"></i> Hidrantes
                                    </a>
                                </li>
                            @endif
                        @endif
                    </ul>

                    <!-- Indicador de rol (lado derecho) -->
                    @if(auth()->user()->role === 'Desarrollador')
                        <span class="navbar-text text-danger">
                            <i class="bi bi-tools"></i> MODO DESARROLLADOR
                        </span>
                    @endif
                </div>
            </div>
        </nav>
    @endauth

    <!-- Contenido principal antes tenia: class="py-4" -->
    <main>
        <div class="container-fluid p-0">
            @yield('content')
        </div>
    </main>

    <!-- Footer existente -->
    <footer class="bg-light text-dark py-2 mt-2">
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

    // AJAX setup for CSRF token
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    </script>
</body>
</html>