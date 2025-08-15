<!-- Archivo A SIGEM-->
@extends('layouts.app')
@section('title', 'Sistema de Información Geográfica y Estadística Municipal (SIGEM)')

@section('content')
    <style>
        .header-logos {
            display: flex;
            width: 100%;
            min-height: 100px;
            border-bottom: 4px solid #ffd700;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .logo-section {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: white;
            margin: 10px 5px;
            border-radius: 8px;
            padding: 15px;
            transition: all 0.3s ease;
        }

        .logo-section:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }

        .logo-section img {
            max-width: 100%;
            max-height: 80px;
            object-fit: contain;
        }

        .main-menu {
            background-color: #48887B !important;
            border-bottom: 3px solid #ffd700;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            width: 100%;
            margin: 0;
            padding: 0;
        }

        .main-menu .nav-container {
            display: flex;
            justify-content: center;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0;
        }

        .main-menu a {
            color: white;
            text-decoration: none;
            padding: 12px 20px;
            font-weight: bold;
            font-size: 14px;
            border-radius: 0;
            transition: all 0.3s ease;
            position: relative;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .main-menu a:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: #ffd700;
            transform: translateY(-1px);
        }

        .main-menu a.active {
            background-color: #0b584fff;
            color: #ffd700;
            font-weight: bold;
        }

        .main-menu a.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background-color: #ffd700;
        }

        .Cargando {
            text-align: center;
            padding: 40px;
            color: #2a6e48;
        }

        .Cargando i {
            font-size: 24px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .highlight-focus {
            background-color: #fff3cd !important;
            border: 2px solid #ffc107 !important;
            box-shadow: 0 0 15px rgba(255, 193, 7, 0.5) !important;
            animation: pulseHighlight 1s ease-in-out;
        }

        @keyframes pulseHighlight {
            0% { transform: scale(1); }
            50% { transform: scale(1.02); }
            100% { transform: scale(1); }
        }

        .product-section {
            display: flex;
            align-items: flex-start;
            gap: 20px;
            margin-bottom: 20px;
        }

        .product-section img {
            max-width: 200px;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .product-text {
            flex: 1;
        }

        .catalogo-row {
            min-height: 600px;
        }

        .catalogo-row .card-body {
            padding: 0;
        }

        .catalogo-row .card-body > div {
            height: 550px;
            overflow-y: auto;
        }

        @media (max-width: 768px) {
            .header-logos {
                flex-direction: column;
                min-height: auto;
            }
            
            .main-menu .nav-container {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .main-menu a {
                padding: 10px 15px;
                font-size: 13px;
            }

            .product-section {
                flex-direction: column;
                text-align: center;
            }

            .product-section img {
                max-width: 100%;
            }

.consulta-express-modal-content {
animation: fadeInUp 0.4s ease-out;
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.consulta-express-tabla-modal .table {
    margin-bottom: 0;
    font-size: 0.9rem;
}

.consulta-express-tabla-modal .table th {
    background-color: #198754 !important;
    color: white;
    font-weight: 600;
    text-align: center;
    border: 1px solid rgba(255,255,255,0.2);
    font-size: 0.85rem;
}

.consulta-express-tabla-modal .table td {
    border: 1px solid #dee2e6;
    vertical-align: middle;
    font-size: 0.85rem;
}

.consulta-express-tabla-modal .table-responsive {
    border-radius: 0.375rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    max-height: 350px;
    overflow-y: auto;
}

.fs-7 { font-size: 0.8rem !important; }

@media (max-width: 768px) {
    .consulta-express-tabla-modal .table {
        font-size: 0.75rem;
    }
    .consulta-express-tabla-modal .table th,
    .consulta-express-tabla-modal .table td {
        padding: 0.4rem 0.2rem;
        font-size: 0.75rem;
    }
}
        }
    </style>

    

    @php
        $img1 = asset('imagenes/logoadmin.png');
        $img2 = asset('imagenes/sige2.png');
    @endphp

    <div class="container-fluid pb-4 bg-fonde img-fluid" >

        <div class="header-logos container-fluid">
            <div class="logo-section">
                <img src="{{ $img1 }}" alt="JRZ Logo">
            </div>
            <div class="logo-section">
                <img src="{{ $img2 }}" alt="SIGEM Logo">
            </div>
        </div>
        @php
            // Detectar si estamos en una ruta especial de estadística tema o subtema
            $currentPath = request()->path();
            $isEstadisticaEspecial = Str::contains($currentPath, 'estadistica-por-tema');
            
            // Asegurar que $section siempre tenga un valor
            $section = $section ?? request()->query('section', 'inicio');
            
            // Si estamos en una ruta especial de estadística, asegurar que section sea 'estadistica'
            if ($isEstadisticaEspecial) {
                $section = 'estadistica';
            }
        @endphp
        <div class="main-menu container-fluid p-0">
            <div class="nav-container">
                @php
                    // Mejor detección de sección activa
                    $currentPath = request()->path();
                    
                    // Inicializar con el valor del parámetro 'section' o 'inicio' por defecto
                    $currentSection = request()->query('section', 'inicio');
                    
                    // Detección robusta para rutas especiales
                    if (Str::contains($currentPath, 'estadistica-por-tema')) {
                        $currentSection = 'estadistica';
                    } elseif (Str::contains($currentPath, '/cartografia')) {
                        $currentSection = 'cartografia';
                    } elseif (Str::contains($currentPath, '/productos')) {
                        $currentSection = 'productos';
                    } elseif (Str::contains($currentPath, '/catalogo')) {
                        $currentSection = 'catalogo';
                    }
                    
                    // Si estamos en una URL que termina con alguna de estas secciones, también la marcamos como activa
                    foreach (['inicio', 'catalogo', 'estadistica', 'cartografia', 'productos'] as $section) {
                        if (Str::endsWith($currentPath, $section)) {
                            $currentSection = $section;
                            break;
                        }
                    }
                @endphp

                <a href="{{ url('/sigem?section=inicio') }}" 
                   class="sigem-nav-link {{ $currentSection === 'inicio' ? 'active' : '' }}">
                    <i class="bi bi-house-fill"></i> INICIO
                </a>

                <a href="{{ url('/sigem?section=catalogo') }}" 
                   class="sigem-nav-link {{ $currentSection === 'catalogo' ? 'active' : '' }}">
                    <i class="bi bi-journal-text"></i> CATÁLOGO
                </a>

                <a href="{{ url('/sigem?section=estadistica') }}" 
                   class="sigem-nav-link {{ $currentSection === 'estadistica' ? 'active' : '' }}">
                    <i class="bi bi-bar-chart-fill"></i> ESTADÍSTICA
                </a>

                <a href="{{ url('/sigem?section=cartografia') }}" 
                   class="sigem-nav-link {{ $currentSection === 'cartografia' ? 'active' : '' }}">
                    <i class="bi bi-map-fill"></i> CARTOGRAFÍA
                </a>

                <a href="{{ url('/sigem?section=productos') }}" 
                   class="sigem-nav-link {{ $currentSection === 'productos' ? 'active' : '' }}">
                    <i class="bi bi-box-seam"></i> PRODUCTOS
                </a>
            </div>
        </div>

        
        <div id="sigem-content" class="container my-4" {!! $isEstadisticaEspecial || $section === 'estadistica' ? 'style="display:none;"' : '' !!}>
            @yield('dynamic_content')
        </div>

        @if(($section === 'estadistica') || $isEstadisticaEspecial)
            <div class="container my-4">
                @include('partials.estadistica')
            </div>
        @endif

    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/sigem.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            let currentSection = urlParams.get('section') || 'inicio';
            
            const currentPath = window.location.pathname;
            if (currentPath.includes('estadistica-por-tema')) {
                currentSection = 'estadistica';
            } else if (currentPath.includes('/cartografia')) {
                currentSection = 'cartografia';
            } else if (currentPath.includes('/productos')) {
                currentSection = 'productos';
            } else if (currentPath.includes('/catalogo')) {
                currentSection = 'catalogo';
            }
            
            document.querySelectorAll('.sigem-nav-link').forEach(link => {

                link.classList.remove('active');
                
                if (link.textContent.trim().includes('INICIO') && currentSection === 'inicio') {
                    link.classList.add('active');
                } else if (link.textContent.trim().includes('CATÁLOGO') && currentSection === 'catalogo') {
                    link.classList.add('active');
                } else if (link.textContent.trim().includes('ESTADÍSTICA') && currentSection === 'estadistica') {
                    link.classList.add('active');
                } else if (link.textContent.trim().includes('CARTOGRAFÍA') && currentSection === 'cartografia') {
                    link.classList.add('active');
                } else if (link.textContent.trim().includes('PRODUCTOS') && currentSection === 'productos') {
                    link.classList.add('active');
                }
            });
        });
    </script>
@endsection