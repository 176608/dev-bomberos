@extends('VisorSIGEM.layouts.visor')

@section('visor_title', 'SIGEM v2 — Cartografía')

@section('visor_content')
<style>
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

.cartografia-intro-image {
    position: relative;
    overflow: hidden;
    border-radius: 10px;
    margin: 0 auto;
}

.cartografia-intro-image img {
    transition: all 0.3s ease;
    margin: 0 auto;
    display: block;
}

.cartografia-intro-image:hover img {
    transform: scale(1.05);
}

.cartografia-intro-text .lead {
    color: #2a6e48;
    font-weight: 600;
}

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
    cursor: pointer;
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

@media (max-width: 768px) {
    .external-map-image-container {
        height: 100px;
    }
    .external-map-content {
        padding: 15px;
    }
}

@media (max-width: 576px) {
    .external-map-image-container {
        height: 80px;
    }
}
</style>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="row mb-4 align-items-center">
            <div class="col-lg-4 col-md-5 mb-3 mb-md-0 text-center">
                <div class="cartografia-intro-image mx-auto" style="max-width: 80%;">
                    <img src="{{ asset('imagenes/cartogde.png') }}" alt="Cartografía" class="img-fluid rounded shadow-sm">
                </div>
            </div>
            <div class="col-lg-8 col-md-7">
                <div class="cartografia-intro-text">
                    <p class="lead">
                        Cambio para esta vista.
                    </p>
                </div>
            </div>
        </div>

        <div class="row mt-5 cartografia-footer">
            <div class="col-12">
                <hr class="my-4">
                <h5 class="text-center mb-4 text-muted">
                    <i class="bi bi-globe me-2"></i>Para ver mapas visita:
                </h5>
            </div>

            <div class="col-md-6 mb-3">
                <div class="external-map-card h-100">
                    <div class="external-map-image-container" onclick="window.open('https://www.imip.org.mx/imip/node/53', '_blank')">
                        <img src="{{ asset('imagenes/imip_mapas.png') }}" alt="IMIP Mapas Digitales" class="external-map-image">
                        <div class="external-map-overlay">
                            <i class="bi bi-box-arrow-up-right fs-1"></i>
                        </div>
                    </div>
                    <div class="external-map-content p-3">
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

            <div class="col-md-6 mb-3">
                <div class="external-map-card h-100">
                    <div class="external-map-image-container" onclick="window.open('https://sigimip.org.mx/', '_blank')">
                        <img src="{{ asset('imagenes/sigimip_mapas.png') }}" alt="SIGIMIP" class="external-map-image">
                        <div class="external-map-overlay">
                            <i class="bi bi-box-arrow-up-right fs-1"></i>
                        </div>
                    </div>
                    <div class="external-map-content p-3">
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
@endsection