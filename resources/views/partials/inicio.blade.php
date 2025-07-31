<div class="card shadow-sm">
    <div class="card-body">
        <h2 class="text-success mb-4 text-center">
            <i class="bi bi-house-fill me-2"></i>Bienvenido al SIGEM
        </h2>
        
        <div class="row mb-4">
            <div class="col-md-12">
                <p class="lead">
                    Bienvenidos al portal del <strong>Sistema de Información Geográfica y Estadística Municipal, SIGEM</strong>, creado por el Instituto Municipal de Investigación y Planeación (<strong>IMIP</strong>) del Municipio de Juárez, el cual provee información estadística y cartográfica confiable, de calidad y alineada a estándares internacionales.
                </p>
                <p class="lead">
                    Está dirigido a dependencias del sector público y privado, el sector educativo, organizaciones de la sociedad civil y al público en general. Tiene el propósito de apoyar la toma de decisiones para la gestión, diseño e instrumentación de políticas públicas, en beneficio de los habitantes del Municipio de  Juárez.
                </p>
                <p class="lead">
                    Nuestro compromiso es que a través de la disponibilidad de información se logre un desarrollo integral, equilibrado y sostenido para todos los sectores que componen el Municipio de Juárez, para ello la información se concentra en tres módulos:
                </p>
            </div>
        </div>


        <!-- SECCIONES PRINCIPALES -->
        <div class="row mb-4">
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-journal-text me-2"></i>Catálogo
                        </h5>
                    </div>
                    <div class="card-body">
                        <p>Explora nuestro catálogo completo de cuadros estadísticos organizados por temas y subtemas.</p>
                        <button class="btn btn-success" onclick="loadContent('catalogo')">
                            <i class="bi bi-arrow-right me-1"></i>Ver Catálogo
                        </button>
                        <img src="imagenes/iconoesta2.png" alt="Icono Estadística">
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-map-fill me-2"></i>Estadística
                            <img src="../imagenes/iconoesta2.png" alt="Icono Estadística">
                        </h5>
                    </div>
                    <div class="card-body">
                        <p>menu navegable de cuadros estadisticos por tema y subtema.</p>
                        <button class="btn btn-primary" onclick="loadContent('estadistica')">
                            <i class="bi bi-arrow-right me-1"></i>Ver Estadísticas
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-map-fill me-2"></i>Cartografía
                            <img src="../imagenes/cartogde.png" alt="Cartografía">
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