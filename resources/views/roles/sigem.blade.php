<!-- Archivo SIGEM - NO ELIMINAR COMENTARIO -->
@extends('layouts.app')

@section('title', 'SIGEM - Sistema de Información Geográfica')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="bg-primary text-white p-4 rounded mb-4">
                <h1 class="h2 mb-0">
                    <i class="bi bi-geo-alt"></i> SIGEM
                </h1>
                <p class="mb-0">Sistema de Información Geográfica y Estadística Municipal</p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="bi bi-map display-4 text-success mb-3"></i>
                    <h5 class="card-title">Cartografía</h5>
                    <p class="card-text">Visualización de mapas y datos geoespaciales</p>
                    <a href="#" class="btn btn-success" onclick="loadSigemModule('cartografia')">
                        <i class="bi bi-eye"></i> Ver Mapas
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="bi bi-bar-chart display-4 text-info mb-3"></i>
                    <h5 class="card-title">Estadísticas</h5>
                    <p class="card-text">Consulta de datos estadísticos municipales</p>
                    <a href="#" class="btn btn-info" onclick="loadSigemModule('estadistica')">
                        <i class="bi bi-graph-up"></i> Ver Estadísticas
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="bi bi-building display-4 text-warning mb-3"></i>
                    <h5 class="card-title">Inventario Urbano</h5>
                    <p class="card-text">Consulta del inventario urbano municipal</p>
                    <a href="#" class="btn btn-warning" onclick="loadSigemModule('inventario')">
                        <i class="bi bi-building"></i> Ver Inventario
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenedor para cargar módulos SIGEM -->
    <div id="sigem-content" class="mt-4">
        <!-- Aquí se cargarán los módulos PHP existentes via AJAX/iframe -->
    </div>
</div>
@endsection

@section('scripts')
<script>
function loadSigemModule(module) {
    const contentDiv = document.getElementById('sigem-content');
    
    // Usar rutas del SIGEM original (mantener funcionalidad)
    const moduleMap = {
        'geografico': '/geografico',  // ← Ruta del SIGEM original
        'temas': '{{ route("subtema.index") }}',  // ← Ruta del SIGEM original
        'contenido': '/contenido-tema',  // ← Ruta del SIGEM original
    };
    
    if (moduleMap[module]) {
        contentDiv.innerHTML = `
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Módulo SIGEM: ${module.charAt(0).toUpperCase() + module.slice(1)}</h5>
                    <button class="btn btn-sm btn-outline-secondary" onclick="closeSigemModule()">
                        <i class="bi bi-x"></i> Cerrar
                    </button>
                </div>
                <div class="card-body p-0">
                    <iframe src="${moduleMap[module]}" 
                            style="width: 100%; height: 600px; border: none;"
                            title="Módulo ${module}">
                    </iframe>
                </div>
            </div>
        `;
    }
}

function closeSigemModule() {
    document.getElementById('sigem-content').innerHTML = '';
}
</script>
@endsection