<!-- Archivo SIGEM - NO ELIMINAR COMENTARIO -->
@extends('layouts.app')

@section('title', 'SIGEM Administrador')

@section('content')
<div class="container-fluid">
    @if(auth()->user()->hasRole('Desarrollador'))
        <div class="alert alert-warning">
            <i class="bi bi-tools"></i> Accediendo como Desarrollador
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="bg-danger text-white p-4 rounded mb-4">
                <h1 class="h2 mb-0">
                    <i class="bi bi-shield-check"></i> SIGEM Administrador
                </h1>
                <p class="mb-0">Panel de administración del Sistema de Información Geográfica</p>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Gestión de Estadísticas -->
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-graph-up text-primary"></i> Gestión de Estadísticas
                    </h5>
                    <p class="card-text">Crear, editar y eliminar cuadros estadísticos</p>
                    <div class="d-grid gap-2">
                        <a href="#" class="btn btn-primary" onclick="loadAdminModule('cuadroEstadistico')">
                            <i class="bi bi-plus-circle"></i> Gestionar Estadísticas
                        </a>
                        <a href="#" class="btn btn-outline-primary" onclick="loadAdminModule('cuadroEstadistico_agregar')">
                            <i class="bi bi-plus"></i> Agregar Nueva
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gestión de Categorías -->
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-tags text-success"></i> Gestión de Categorías
                    </h5>
                    <p class="card-text">Administrar categorías y temas estadísticos</p>
                    <div class="d-grid gap-2">
                        <a href="#" class="btn btn-success" onclick="loadAdminModule('catalogo')">
                            <i class="bi bi-list-ul"></i> Ver Catálogo
                        </a>
                        <a href="#" class="btn btn-outline-success" onclick="loadAdminModule('crear_tabla de categorias')">
                            <i class="bi bi-plus-square"></i> Crear Categoría
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gestión de Archivos CSV -->
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-file-earmark-spreadsheet text-warning"></i> Archivos CSV
                    </h5>
                    <p class="card-text">Cargar y editar datos desde archivos CSV</p>
                    <div class="d-grid gap-2">
                        <a href="#" class="btn btn-warning" onclick="loadAdminModule('mostrar_csv')">
                            <i class="bi bi-eye"></i> Ver CSV
                        </a>
                        <a href="#" class="btn btn-outline-warning" onclick="loadAdminModule('editar_csv')">
                            <i class="bi bi-pencil"></i> Editar CSV
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gestión de Reportes -->
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-file-pdf text-danger"></i> Reportes
                    </h5>
                    <p class="card-text">Generar reportes y gráficas</p>
                    <div class="d-grid gap-2">
                        <a href="#" class="btn btn-danger" onclick="loadAdminModule('guardar_pdf')">
                            <i class="bi bi-file-pdf"></i> Generar PDF
                        </a>
                        <a href="#" class="btn btn-outline-danger" onclick="loadAdminModule('generarGrafica')">
                            <i class="bi bi-bar-chart"></i> Generar Gráfica
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenedor para módulos de administración -->
    <div id="sigem-admin-content" class="mt-4">
        <!-- Aquí se cargarán los módulos de administración -->
    </div>
</div>
@endsection

@section('scripts')
<script>
function loadAdminModule(module) {
    const contentDiv = document.getElementById('sigem-admin-content');
    
    // Mapeo de módulos administrativos
    const adminModuleMap = {
        'cuadroEstadistico': '{{ asset("vistas_SIGEM/cuadroEstadistico.php") }}',
        'cuadroEstadistico_agregar': '{{ asset("vistas_SIGEM/cuadroEstadistico_agregar.php") }}',
        'cuadroEstadistico_editar': '{{ asset("vistas_SIGEM/cuadroEstadistico_editar.php") }}',
        'catalogo': '{{ asset("vistas_SIGEM/catalogo.php") }}',
        'crear_tabla de categorias': '{{ asset("vistas_SIGEM/crear_tabla de categorias.php") }}',
        'mostrar_csv': '{{ asset("vistas_SIGEM/mostrar_csv.php") }}',
        'editar_csv': '{{ asset("vistas_SIGEM/editar_csv.php") }}',
        'guardar_pdf': '{{ asset("vistas_SIGEM/guardar_pdf.php") }}',
        'generarGrafica': '{{ asset("vistas_SIGEM/generarGrafica.php") }}'
    };
    
    if (adminModuleMap[module]) {
        contentDiv.innerHTML = `
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-tools"></i> Módulo Administrativo: ${module.replace(/_/g, ' ')}
                    </h5>
                    <button class="btn btn-sm btn-outline-secondary" onclick="closeAdminModule()">
                        <i class="bi bi-x"></i> Cerrar
                    </button>
                </div>
                <div class="card-body p-0">
                    <iframe src="${adminModuleMap[module]}" 
                            style="width: 100%; height: 700px; border: none;"
                            title="Módulo administrativo ${module}">
                    </iframe>
                </div>
            </div>
        `;
    }
}

function closeAdminModule() {
    document.getElementById('sigem-admin-content').innerHTML = '';
}
</script>
@endsection administrador necesario