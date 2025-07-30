<!-- Archivo SIGEM - Base de vista sigem publica - NO ELIMINAR COMENTARIO -->
@extends('layouts.app')
@section('title', 'SIGEM - Sistema de Información Geográfica')

@section('content')
<style>
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

/* NUEVO: Menú SIGEM */
.main-menu {
    background-color: #2a6e48 !important;
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
    background-color: #1e4d35;
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

/* Cargando indicator */
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

/* Estilos para el contenido dinámico */
.main-card {
    background-color: white;
    border-radius: 12px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    padding: 30px;
    margin-bottom: 20px;
}

.estadistica-header {
    display: flex;
    align-items: flex-start;
    gap: 20px;
    margin-bottom: 30px;
}

.estadistica-header img {
    width: 80px;
    height: auto;
    flex-shrink: 0;
}

.botones-temas {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 15px;
    margin: 30px 0;
}

.botones-temas a {
    background: linear-gradient(135deg, #2a6e48, #66d193);
    color: white;
    padding: 15px 20px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: bold;
    text-align: center;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.botones-temas a:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    color: white;
}

.catalogo {
    text-align: center;
    padding: 20px;
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border-radius: 8px;
}

.catalogo a {
    color: #2a6e48;
    text-decoration: none;
    font-weight: bold;
    font-size: 18px;
}

.catalogo a:hover {
    color: #1e4d35;
}

.product-section {
    display: flex;
    gap: 20px;
    margin-bottom: 30px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 8px;
}

.product-section img {
    width: 120px;
    height: auto;
    flex-shrink: 0;
    border-radius: 4px;
}

.product-text h5 {
    color: #2a6e48;
    margin-bottom: 10px;
}

.map-section {
    margin: 30px 0;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 8px;
}

.map-section iframe {
    width: 100%;
    height: 400px;
    border: 2px solid #dee2e6;
    border-radius: 4px;
}

.title-row {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 20px;
}

.title-row img {
    width: 60px;
    height: auto;
}

.intro-text {
    font-size: 16px;
    color: #6c757d;
    margin-bottom: 30px;
}

/* NUEVOS ESTILOS: Efectos para índice y focus */
.indice-tema-header:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2) !important;
}

.indice-subtema-row:hover {
    background-color: #e8f4f8 !important;
    transform: translateX(5px) !important;
}

.highlight-focus {
    background-color: #fff3cd !important;
    border: 2px solid #ffc107 !important;
    box-shadow: 0 0 15px rgba(255, 193, 7, 0.5) !important;
    animation: pulseHighlight 1s ease-in-out;
}

@keyframes pulseHighlight {
    0% { 
        transform: scale(1); 
        box-shadow: 0 0 15px rgba(255, 193, 7, 0.5);
    }
    50% { 
        transform: scale(1.02); 
        box-shadow: 0 0 25px rgba(255, 193, 7, 0.8);
    }
    100% { 
        transform: scale(1); 
        box-shadow: 0 0 15px rgba(255, 193, 7, 0.5);
    }
}

/* Asegurar alturas iguales */
#indice-container, #cuadros-container {
    overflow-y: auto;
}

/* AGREGAR: Asegurar que ambos contenedores tengan alturas mínimas iguales */
.catalogo-row {
    display: flex;
    align-items: stretch;
}

.catalogo-row .card {
    height: 100%;
}

.catalogo-row .card-body {
    display: flex;
    flex-direction: column;
    height: 100%;
}

/* Responsive */
@media (max-width: 768px) {
    .header-logos {
        flex-direction: column;
        min-height: auto;
    }
    
    .logo-section {
        margin: 5px 10px;
    }
    
    .main-menu .nav-container {
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .main-menu a {
        padding: 10px 15px;
        font-size: 13px;
    }
    
    .estadistica-header {
        flex-direction: column;
        text-align: center;
    }
    
    .product-section {
        flex-direction: column;
        text-align: center;
    }
    
    .product-section img {
        align-self: center;
    }
    
    .botones-temas {
        grid-template-columns: 1fr;
    }
}
</style>

    @php
        $img1 = asset('imagenes/logoadmin.png');
        $img2 = asset('imagenes/sige2.png');
    @endphp

    <div class="container-fluid" style="background: linear-gradient(135deg, #2a6e48 0%, #66d193 50%, #2a6e48 100%);">
        <!-- Sección de Logos -->
        <div class="header-logos container-fluid">
            <div class="logo-section">
                <img src="{{ $img1 }}" alt="JRZ Logo">
            </div>
            <div class="logo-section">
                <img src="{{ $img2 }}" alt="SIGEM Logo">
            </div>
        </div>

        <!-- MENÚ SIGEM -->
        <div class="main-menu container-fluid p-0">
            <div class="nav-container">
                <!-- ELIMINAR: inicio, cambiar orden -->
                <a href="#" data-section="catalogo" class="sigem-nav-link active">
                    <i class="bi bi-journal-text"></i> CATÁLOGO
                </a>
                <a href="#" data-section="estadistica" class="sigem-nav-link">
                    <i class="bi bi-bar-chart-fill"></i> ESTADÍSTICA
                </a>
                <a href="#" data-section="cartografia" class="sigem-nav-link">
                    <i class="bi bi-map-fill"></i> CARTOGRAFÍA
                </a>
                <a href="#" data-section="productos" class="sigem-nav-link">
                    <i class="bi bi-box-seam"></i> PRODUCTOS
                </a>
            </div>
        </div>

        <!-- CONTENEDOR DINÁMICO -->
        <div id="sigem-content" class="container mt-4">
            @yield('dynamic_content')
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/sigem.js') }}"></script>
@endsection