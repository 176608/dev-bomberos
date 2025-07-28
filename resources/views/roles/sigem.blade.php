<!-- Archivo SIGEM -Base de vista sigem publica- - NO ELIMINAR COMENTARIO -->
@extends('layouts.app')

@section('title', 'SIGEM - Sistema de Información Geográfica')

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

/* Loading indicator */
.loading {
    text-align: center;
    padding: 40px;
    color: #2a6e48;
}

.loading i {
    font-size: 24px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
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
}
</style>

<div class="container-fluid" style="background: linear-gradient(135deg, #2a6e48 0%, #66d193 50%, #2a6e48 100%);">

    <div class="header-logos container-fluid">
        <div class="logo-section">
            <img src="../imagenes/logoadmin.png" alt="JRZ Logo">
        </div>
        <div class="logo-section">
            <img src="../imagenes/sige2.png" alt="SIGEM Logo">
        </div>
    </div>

    <!-- MENÚ SIGEM -->
    <div class="main-menu container-fluid p-0">
        <div class="nav-container">
            <a href="#" data-section="inicio" class="sigem-nav-link active">
                <i class="bi bi-house-fill"></i> INICIO
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
            <a href="#" data-section="catalogo" class="sigem-nav-link">
                <i class="bi bi-journal-text"></i> CATÁLOGO
            </a>
        </div>
    </div>

    <!-- CONTENEDOR DINÁMICO -->
    <div id="sigem-content" class="container mt-4">
        <div class="loading">
            <i class="bi bi-hourglass-split"></i>
            <p>Cargando contenido...</p>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Elementos del DOM
    const navLinks = document.querySelectorAll('.sigem-nav-link');
    const contentContainer = document.getElementById('sigem-content');
    
    // Contenidos simulados (luego serán partials reales)
    const partialContent = {
        inicio: `
            <div class="row">
                <div class="col-12">
                    <h2 class="text-success mb-4">
                        <i class="bi bi-house-fill"></i> Bienvenido al SIGEM
                    </h2>
                    <div class="card">
                        <div class="card-body">
                            <p class="lead">Sistema de Información Geográfica y Estadística Municipal</p>
                            <p>Explora los datos geográficos y estadísticos de Ciudad Juárez.</p>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body text-center">
                                            <h4>125</h4>
                                            <p>Mapas Disponibles</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-success text-white">
                                        <div class="card-body text-center">
                                            <h4>50</h4>
                                            <p>Indicadores</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-info text-white">
                                        <div class="card-body text-center">
                                            <h4>2024</h4>
                                            <p>Última Actualización</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `,
        estadistica: `
            <div class="row">
                <div class="col-12">
                    <h2 class="text-success mb-4">
                        <i class="bi bi-bar-chart-fill"></i> Estadísticas Municipales
                    </h2>
                    <div class="card">
                        <div class="card-body">
                            <p>Módulo de estadísticas en desarrollo...</p>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> 
                                Aquí se mostrarán gráficos y datos estadísticos
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `,
        cartografia: `
            <div class="row">
                <div class="col-12">
                    <h2 class="text-success mb-4">
                        <i class="bi bi-map-fill"></i> Cartografía Digital
                    </h2>
                    <div class="card">
                        <div class="card-body">
                            <p>Visualizador de mapas interactivos...</p>
                            <div id="map-placeholder" style="height: 400px; background: #f8f9fa; border: 2px dashed #dee2e6; display: flex; align-items: center; justify-content: center;">
                                <span class="text-muted">
                                    <i class="bi bi-geo-alt display-4"></i><br>
                                    Mapa interactivo aquí
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `,
        productos: `
            <div class="row">
                <div class="col-12">
                    <h2 class="text-success mb-4">
                        <i class="bi bi-box-seam"></i> Productos Cartográficos
                    </h2>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5>Mapas Temáticos</h5>
                                    <p>Descarga mapas especializados</p>
                                    <button class="btn btn-success">Ver Catálogo</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5>Datos Geoespaciales</h5>
                                    <p>Archivos SHP, KML, GeoJSON</p>
                                    <button class="btn btn-info">Descargar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `,
        catalogo: `
            <div class="row">
                <div class="col-12">
                    <h2 class="text-success mb-4">
                        <i class="bi bi-journal-text"></i> Catálogo de Datos
                    </h2>
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead class="table-success">
                                        <tr>
                                            <th>Dataset</th>
                                            <th>Categoría</th>
                                            <th>Última Actualización</th>
                                            <th>Formato</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Uso de Suelo</td>
                                            <td>Cartografía</td>
                                            <td>2024-01-15</td>
                                            <td>SHP</td>
                                            <td><button class="btn btn-sm btn-success">Ver</button></td>
                                        </tr>
                                        <tr>
                                            <td>Población por Colonia</td>
                                            <td>Estadística</td>
                                            <td>2024-01-10</td>
                                            <td>CSV</td>
                                            <td><button class="btn btn-sm btn-info">Descargar</button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `
    };
    
    // Función para cargar contenido
    function loadContent(section) {
        // Mostrar loading
        contentContainer.innerHTML = `
            <div class="loading">
                <i class="bi bi-hourglass-split"></i>
                <p>Cargando ${section}...</p>
            </div>
        `;
        
        // Simular delay de carga
        setTimeout(() => {
            if (partialContent[section]) {
                contentContainer.innerHTML = partialContent[section];
            } else {
                contentContainer.innerHTML = `
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        Contenido de <strong>${section}</strong> no disponible
                    </div>
                `;
            }
        }, 500);
    }
    
    // Event listeners para navegación
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const section = this.getAttribute('data-section');
            
            // Remover active de todos
            navLinks.forEach(l => l.classList.remove('active'));
            
            // Agregar active al clickeado
            this.classList.add('active');
            
            // Cargar contenido
            loadContent(section);
            
            console.log(`Cargando sección: ${section}`);
        });
    });
    
    // Cargar contenido inicial
    loadContent('inicio');
});
</script>
@endsection