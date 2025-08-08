<div class="card shadow-sm">
    <div class="card-body">
        

        <!-- NUEVA SECCIÓN: Imagen + Texto descriptivo -->
        <div class="row mb-4 align-items-center">
            <div class="col-lg-4 col-md-5 mb-3 mb-md-0 text-center">
                <div class="cartografia-intro-image mx-auto" style="max-width: 80%;">
                    <img src="{{ asset('imagenes/cartogde.png') }}" alt="Cartografía" class="img-fluid rounded shadow-sm">
                </div>
            </div>
            <div class="col-lg-8 col-md-7">
                <div class="cartografia-intro-text">
                    <p class="lead">
                        En este apartado podrás encontrar <strong>mapas temáticos interactivos</strong> del Municipio de Juárez.
                    </p>
                </div>
            </div>
        </div>

        
        <div id="mapas-container">
            <div class="text-center py-3">
                <i class="bi bi-hourglass-split"></i>
                <p>Cargando mapas...</p>
            </div>
        </div>

        <!-- NUEVA SECCIÓN: Footer con botones externos -->
        <div class="row mt-5 cartografia-footer">
            <div class="col-12">
                <hr class="my-4">
                <h5 class="text-center mb-4 text-muted">
                    <i class="bi bi-globe me-2"></i>Para ver otros mapas visita:
                </h5>
            </div>
            
            <!-- Botón IMIP Mapas Digitales -->
            <div class="col-md-6 mb-3">
                <div class="external-map-card h-100">
                    <div class="external-map-image-container" onclick="window.open('https://www.imip.org.mx/imip/node/53', '_blank')">
                        <img src="{{ asset('imagenes/imip_mapas.png') }}" alt="IMIP Mapas Digitales" class="external-map-image">
                        <div class="external-map-overlay">
                            <i class="bi bi-box-arrow-up-right fs-1"></i>
                        </div>
                    </div>
                    <div class="external-map-content">
                        <h6 class="fw-bold text-success mb-2">IMIP Mapas Digitales Interactivos</h6>
                        <p class="text-muted small mb-3">
                            Accede a la plataforma especializada de mapas interactivos del Instituto Municipal de Investigación y Planeación.
                        </p>
                        <a href="https://www.imip.org.mx/imip/node/53" class="btn btn-success btn-sm w-100" target="_blank">
                            <i class="bi bi-box-arrow-up-right me-1"></i>Visitar IMIP Mapas
                        </a>
                    </div>
                </div>
            </div>

            <!-- Botón SIGIMIP -->
            <div class="col-md-6 mb-3">
                <div class="external-map-card h-100">
                    <div class="external-map-image-container" onclick="window.open('https://sigimip.org.mx/', '_blank')">
                        <img src="{{ asset('imagenes/sigimip_mapas.png') }}" alt="SIGIMIP" class="external-map-image">
                        <div class="external-map-overlay">
                            <i class="bi bi-box-arrow-up-right fs-1"></i>
                        </div>
                    </div>
                    <div class="external-map-content">
                        <h6 class="fw-bold text-primary mb-2">SIGIMIP</h6>
                        <p class="text-muted small mb-3">
                            Sistema de Información Geográfica del Instituto Municipal de Investigación y Planeación con herramientas avanzadas.
                        </p>
                        <a href="https://sigimip.org.mx/" class="btn btn-primary btn-sm w-100" target="_blank">
                            <i class="bi bi-box-arrow-up-right me-1"></i>Visitar SIGIMIP
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Estilos específicos para cartografía */
.mapa-row {
    margin-bottom: 30px;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.mapa-row:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    transform: translateY(-2px);
}

.mapa-header {
    background: linear-gradient(135deg, #2a6e48 0%, #66d193 100%);
    padding: 15px 20px;
}

.mapa-title {
    color: white;
    font-weight: bold;
    font-size: 1.2em;
    margin: 0;
}

.mapa-seccion {
    color: #ffd700;
    font-size: 0.9em;
    margin: 0;
}

.mapa-btn {
    background-color: #ffd700;
    color: #2a6e48;
    border: none;
    padding: 8px 16px;
    border-radius: 5px;
    font-weight: bold;
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    white-space: nowrap;
    font-size: 0.9em;
}

.mapa-btn:hover {
    background-color: #ffed4e;
    color: #1e4d35;
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    text-decoration: none;
}

.mapa-btn-disabled {
    background-color: #6c757d !important;
    cursor: not-allowed !important;
    opacity: 0.6;
}

.mapa-btn-disabled:hover {
    transform: none !important;
    box-shadow: none !important;
}

/* NUEVO: Contenedor de imagen y descripción */
.mapa-content {
    display: flex;
    min-height: 200px;
    background-color: white;
}

.mapa-image-container {
    flex: 0 0 50%;
    position: relative;
    overflow: hidden;
    background: linear-gradient(45deg, #f8f9fa 0%, #e9ecef 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.mapa-image-container[style*="cursor: pointer"]:hover {
    background: linear-gradient(45deg, #e9ecef 0%, #dee2e6 100%);
}

.mapa-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: all 0.4s ease;
}

.mapa-image:hover {
    transform: scale(1.05);
    filter: brightness(1.1) contrast(1.1);
}

.mapa-image-placeholder {
    text-align: center;
    color: #6c757d;
    padding: 40px 20px;
}

.mapa-image-placeholder i {
    font-size: 3em;
    margin-bottom: 15px;
    display: block;
    color: #2a6e48;
}

.mapa-image-placeholder h5 {
    color: #2a6e48;
    margin-bottom: 10px;
    font-weight: bold;
}

.mapa-image-placeholder p {
    margin: 0;
    font-size: 0.9em;
}

.mapa-descripcion {
    flex: 1;
    padding: 20px;
    background-color: white;
    border-left: 1px solid #e0e0e0;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.mapa-descripcion h5 {
    color: #2a6e48;
    margin-bottom: 15px;
    font-weight: bold;
    display: flex;
    align-items: center;
    gap: 8px;
}

.mapa-descripcion p {
    color: #495057;
    line-height: 1.6;
    margin-bottom: 0;
    text-align: justify;
}

/* Overlay de hover en imagen */
.mapa-image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(42, 110, 72, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: all 0.3s ease;
    color: white;
    font-size: 1.2em;
    font-weight: bold;
}

.mapa-image-container:hover .mapa-image-overlay {
    opacity: 1;
}

/* Estilos para manejo de errores de imagen */
.mapa-image-container.image-error .mapa-image-overlay {
    display: none;
}

.mapa-image-container.image-error .image-error-placeholder {
    display: flex !important;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
}

/* === NUEVOS ESTILOS PARA SECCIÓN INTRO === */
.cartografia-intro-image {
    position: relative;
    overflow: hidden;
    border-radius: 10px;
    margin: 0 auto; /* Centra el contenedor */
}

.cartografia-intro-image img {
    transition: all 0.3s ease;
    margin: 0 auto; /* Centra la imagen */
    display: block; /* Necesario para que el margen automático funcione */
}

.cartografia-intro-image:hover img {
    transform: scale(1.05);
}

.cartografia-intro-text {
    padding-left: 20px;
}

.cartografia-intro-text .lead {
    color: #2a6e48;
    font-weight: 600;
}

/* === NUEVOS ESTILOS PARA FOOTER EXTERNO === */
.cartografia-footer {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 10px;
    padding: 20px;
    margin-top: 30px;
}

.external-map-card {
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    cursor: pointer;
}

.external-map-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.external-map-image-container {
    position: relative;
    height: 120px;
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
    overflow: hidden;
    cursor: pointer; /* AGREGAR: Cursor pointer para indicar clickeable */
}

.external-map-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: all 0.3s ease;
    opacity: 0.8;
}

.external-map-card:hover .external-map-image {
    transform: scale(1.1);
    opacity: 1;
}

.external-map-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.6);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: all 0.3s ease;
    color: white;
}

.external-map-card:hover .external-map-overlay {
    opacity: 1;
}

/* AGREGAR: Efecto adicional en hover de imagen para indicar clickeable */
.external-map-image-container:hover {
    background: linear-gradient(135deg, #34495e 0%, #2c3e50 100%);
}

.external-map-image-container:hover .external-map-overlay i {
    animation: bounce 0.6s ease infinite alternate;
}

@keyframes bounce {
    0% { transform: scale(1); }
    100% { transform: scale(1.1); }
}

/* Responsive */
@media (max-width: 768px) {
    .mapa-header .row {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 10px;
    }
    
    .mapa-header .col-10,
    .mapa-header .col-2 {
        flex: none;
        width: 100%;
    }
    
    .mapa-header .col-2 {
        text-align: left !important;
    }
    
    .mapa-content {
        flex-direction: column;
        min-height: auto;
    }
    
    .mapa-image-container {
        flex: none;
        height: 200px;
    }
    
    .mapa-descripcion {
        border-left: none;
        border-top: 1px solid #e0e0e0;
        padding: 15px;
    }
    
    .cartografia-intro-text {
        padding-left: 0;
        margin-top: 20px;
    }
    
    .external-map-image-container {
        height: 100px;
    }
    
    .external-map-content {
        padding: 15px;
    }
}

@media (max-width: 576px) {
    .mapa-image-container {
        height: 150px;
    }
    
    .mapa-image-placeholder {
        padding: 20px 15px;
    }
    
    .mapa-image-placeholder i {
        font-size: 2em;
        margin-bottom: 10px;
    }
    
    .mapa-image-placeholder h5 {
        font-size: 1em;
        margin-bottom: 5px;
    }
    
    .mapa-image-placeholder p {
        font-size: 0.8em;
    }
    
    .mapa-title {
        font-size: 1.1em;
    }
    
    .mapa-seccion {
        font-size: 0.85em;
    }
    
    .mapa-descripcion {
        padding: 15px;
    }
    
    .mapa-btn {
        font-size: 0.8em;
        padding: 6px 12px;
    }
}
</style>