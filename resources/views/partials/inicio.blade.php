<style>
/* === ESTILOS PARA MÓDULOS CON EFECTO MIRROR === */
.module-card {
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
}

.module-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

.module-image-container {
    cursor: pointer;
    position: relative;
    margin: 0 auto;
    max-width: 200px;
}

.module-image-wrapper {
    position: relative;
    background: #398d4b9a ;
    border-radius: 15px;
    padding: 20px;
    overflow: hidden;
    transition: all 0.4s ease;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
}

.module-image-wrapper::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.1) 50%, transparent 70%);
    opacity: 0;
    transition: all 0.3s ease;
    pointer-events: none;
}

.module-image-wrapper:hover::before {
    opacity: 1;
    transform: translateX(100%);
}

.module-image {
    width: 100%;
    height: auto;
    max-height: 120px;
    object-fit: contain;
    transition: all 0.4s ease;
    filter: drop-shadow(0 5px 15px rgba(0,0,0,0.3));
}

.module-image-wrapper:hover .module-image {
    transform: scale(1.1) rotateY(5deg);
    filter: drop-shadow(0 8px 25px rgba(0,0,0,0.4)) brightness(1.1);
}

.module-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: all 0.3s ease;
    border-radius: 15px;
}

.module-image-wrapper:hover .module-overlay {
    opacity: 1;
}

.module-overlay i {
    transform: scale(0.8);
    transition: all 0.3s ease;
}

.module-image-wrapper:hover .module-overlay i {
    transform: scale(1);
    animation: pulse 1.5s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

/* === EFECTOS ADICIONALES === */
.module-card .card-header {
    border-bottom: 3px solid rgba(255,255,255,0.2);
    position: relative;
    overflow: hidden;
}

.module-card .card-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: all 0.5s ease;
}

.module-card:hover .card-header::before {
    left: 100%;
}

.module-card .btn {
    transition: all 0.3s ease;
    border: none;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.module-card .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
}

/* === RESPONSIVE === */
@media (max-width: 768px) {
    .module-image-wrapper {
        max-width: 150px;
        margin: 0 auto;
    }
    
    .module-image {
        max-height: 80px;
    }
}

@media (max-width: 576px) {
    .module-image-wrapper {
        max-width: 120px;
        padding: 15px;
    }
    
    .module-image {
        max-height: 60px;
    }
}

/* === ESTILOS PARA CONSULTA EXPRESS === */
.consulta-express-container {
    position: relative;
    max-width: 220px;
    margin: 0 auto;
    overflow: hidden;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.consulta-express-container:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.2);
}

.consulta-express-image {
    transition: all 0.4s ease;
    max-height: 220px;
    display: block;
    width: 100%;
}

.consulta-express-container:hover .consulta-express-image {
    transform: scale(1.05);
}

.consulta-express-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(39, 185, 75, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: all 0.3s ease;
}

.consulta-express-container:hover .consulta-express-overlay {
    opacity: 1;
}

.consulta-express-text {
    color: white;
    font-size: 18px;
    font-weight: bold;
    text-align: center;
    padding: 10px;
    transform: translateY(20px);
    transition: all 0.4s ease;
}

.consulta-express-container:hover .consulta-express-text {
    transform: translateY(0);
}
</style>

<div class="card shadow-sm">
    <div class="card-body">
        <h2 class="text-success mb-4 text-center">
            <i class="bi bi-house-fill me-2"></i>Bienvenido a SIGEM
        </h2>
        
        <div class="row mb-4">
            <div class="col-md-9">
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
            <div class="col-md-3 text-center mb-3 mb-md-0">
                <div class="consulta-express-container" onclick="if(typeof SIGEMApp !== 'undefined') SIGEMApp.openConsultaExpress(); else alert('SIGEMApp no está disponible');">
                    <img src="{{ asset('imagenes/express.png') }}" alt="Consulta Express" class="img-fluid rounded shadow consulta-express-image">
                    <div class="consulta-express-overlay">
                        <span class="consulta-express-text">
                            <i class="bi bi-lightning-fill me-2"></i>Consultar Información
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- MÓDULOS PRINCIPALES CON DISEÑO UNIFORME -->
        <div class="row mb-4">
            <!-- CATÁLOGO -->
            <div class="col-lg-4 mb-3">
                <div class="card h-100 shadow-sm module-card">
                    <div class="card-header bg-success text-white text-center">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-journal-text me-2"></i>Catálogo
                        </h5>
                    </div>
                    <div class="card-body text-center p-4">
                        <!-- Imagen con efecto hover -->
                        <div class="module-image-container mb-3" onclick="window.location.href='{{ url('/sigem?section=catalogo') }}'">
                            <div class="module-image-wrapper">
                                <img src="{{ asset('imagenes/sige2.png') }}" alt="Catálogo de Cuadros" class="module-image">
                                <div class="module-overlay">
                                    <i class="bi bi-arrow-right-circle fs-1 text-white"></i>
                                </div>
                            </div>
                        </div>
                        <!-- Descripción -->
                        <p class="text-muted mb-3">
                            Explora nuestro catálogo completo de cuadros estadísticos organizados por temas y subtemas con sistema de navegación intuitivo.
                        </p>
                        <!-- Botón -->
                        <button class="btn btn-success btn-sm" onclick="window.location.href='{{ url('/sigem?section=catalogo') }}'">
                            <i class="bi bi-arrow-right me-1"></i>Ver Catálogo
                        </button>
                    </div>
                </div>
            </div>

            <!-- ESTADÍSTICA -->
            <div class="col-lg-4 mb-3">
                <div class="card h-100 shadow-sm module-card">
                    <div class="card-header bg-success text-white text-center">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-bar-chart-fill me-2"></i>Estadística
                        </h5>
                    </div>
                    <div class="card-body text-center p-4">
                        <!-- Imagen con efecto hover -->
                        <div class="module-image-container mb-3" onclick="window.location.href='{{ url('/sigem?section=estadistica') }}'">
                            <div class="module-image-wrapper">
                                <img src="{{ asset('imagenes/iconoesta2.png') }}" alt="Módulo Estadística" class="module-image">
                                <div class="module-overlay">
                                    <i class="bi bi-arrow-right-circle fs-1 text-white"></i>
                                </div>
                            </div>
                        </div>
                        <!-- Descripción -->
                        <p class="text-muted mb-3">
                            Menú navegable de cuadros estadísticos organizados por tema y subtema para consulta y análisis de datos municipales.
                        </p>
                        <!-- Botón -->
                        <button class="btn btn-success btn-sm" onclick="window.location.href='{{ url('/sigem?section=estadistica') }}'">
                            <i class="bi bi-arrow-right me-1"></i>Ver Estadísticas
                        </button>
                    </div>
                </div>
            </div>

            <!-- CARTOGRAFÍA -->
            <div class="col-lg-4 mb-3">
                <div class="card h-100 shadow-sm module-card">
                    <div class="card-header bg-success text-white text-center">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-geo-alt-fill me-2"></i>Cartografía
                        </h5>
                    </div>
                    <div class="card-body text-center p-4">
                        <!-- Imagen con efecto hover -->
                        <div class="module-image-container mb-3" onclick="window.location.href='{{ url('/sigem?section=cartografia') }}'">
                            <div class="module-image-wrapper">
                                <img src="{{ asset('imagenes/cartogde.png') }}" alt="Módulo Cartografía" class="module-image">
                                <div class="module-overlay">
                                    <i class="bi bi-arrow-right-circle fs-1 text-white"></i>
                                </div>
                            </div>
                        </div>
                        <!-- Descripción -->
                        <p class="text-muted mb-3">
                            Accede a mapas temáticos y cartografía digital del municipio de Juárez con herramientas de visualización geográfica.
                        </p>
                        <!-- Botón -->
                        <button class="btn btn-success btn-sm" onclick="window.location.href='{{ url('/sigem?section=cartografia') }}'">
                            <i class="bi bi-arrow-right me-1"></i>Ver Mapas
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<div class="container">
    @include('partials.inicio_consulta_express')
</div>