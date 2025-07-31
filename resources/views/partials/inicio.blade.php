<div class="card shadow-sm">
    <div class="card-body">
        <h2 class="text-success mb-4 text-center">
            <i class="bi bi-house-fill me-2"></i>Bienvenido al SIGEM
        </h2>
        
        <div class="row mb-4">
            <div class="col-md-8">
                <p class="lead">
                    El <strong>Sistema de Información Geográfica y Estadística Municipal (SIGEM)</strong> 
                    es una plataforma integral que proporciona acceso a datos estadísticos, mapas 
                    cartográficos y productos de información del Municipio de Juárez, Chihuahua.
                </p>
            </div>
            <div class="col-md-4 text-center">
                <img src="{{ asset('imagenes/sige2.png') }}" alt="SIGEM Logo" class="img-fluid" style="max-height: 120px;">
            </div>
        </div>

        <!-- ESTADÍSTICAS GENERALES -->
        <div class="row mb-4" id="estadisticas-container">
            <div class="col-md-4">
                <div class="card text-center bg-success text-white">
                    <div class="card-body">
                        <i class="bi bi-folder-fill fs-1"></i>
                        <h4 class="mt-2 mb-1" id="stat-temas">-</h4>
                        <p class="mb-0">Temas</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center bg-primary text-white">
                    <div class="card-body">
                        <i class="bi bi-collection-fill fs-1"></i>
                        <h4 class="mt-2 mb-1" id="stat-subtemas">-</h4>
                        <p class="mb-0">Subtemas</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center bg-info text-white">
                    <div class="card-body">
                        <i class="bi bi-table fs-1"></i>
                        <h4 class="mt-2 mb-1" id="stat-cuadros">-</h4>
                        <p class="mb-0">Cuadros</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECCIONES PRINCIPALES -->
        <div class="row mb-4">
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-journal-text me-2"></i>Catálogo de Cuadros
                        </h5>
                    </div>
                    <div class="card-body">
                        <p>Explora nuestro catálogo completo de cuadros estadísticos organizados por temas y subtemas.</p>
                        <button class="btn btn-success" onclick="loadContent('catalogo')">
                            <i class="bi bi-arrow-right me-1"></i>Ver Catálogo
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-map-fill me-2"></i>Cartografía
                        </h5>
                    </div>
                    <div class="card-body">
                        <p>Accede a mapas temáticos y cartografía digital del municipio de Juárez.</p>
                        <button class="btn btn-primary" onclick="loadContent('cartografia')">
                            <i class="bi bi-arrow-right me-1"></i>Ver Mapas
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>