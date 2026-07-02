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

.info-section-image-link {
    display: block;
    position: relative;
    overflow: hidden;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    text-decoration: none;
}

.info-section-image-link img {
    transition: all 0.3s ease;
    display: block;
    width: 100%;
}

.info-section-image-link:hover img {
    transform: scale(1.05);
}

.info-section-image-link::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.4);
    opacity: 0;
    transition: all 0.3s ease;
    border-radius: 10px;
}

.info-section-image-link:hover::after {
    opacity: 1;
}

.info-section-image-link .image-overlay-icon {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) scale(0.8);
    color: white;
    font-size: 2.5rem;
    opacity: 0;
    transition: all 0.3s ease;
    z-index: 2;
    text-shadow: 0 2px 8px rgba(0,0,0,0.5);
}

.info-section-image-link:hover .image-overlay-icon {
    opacity: 1;
    transform: translate(-50%, -50%) scale(1);
}

.info-title {
    color: #2a6e48;
    font-weight: 700;
    font-size: 1.4rem;
    border-left: 4px solid #2a6e48;
    padding-left: 15px;
}

.info-text {
    color: #4a4a4a;
    line-height: 1.7;
    text-align: justify;
}

.btn-sigmun {
    background-color: #2a6e48;
    border-color: #2a6e48;
    color: white;
    font-weight: 600;
    padding: 10px 30px;
    transition: all 0.3s ease;
}

.btn-sigmun:hover {
    background-color: #1e5235;
    border-color: #1e5235;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(42, 110, 72, 0.3);
}

@media (max-width: 768px) {
    .info-title {
        font-size: 1.2rem;
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
                        Centro de Información Geoespacial
                    </p>
                </div>
            </div>
        </div>

        <hr class="my-5">

        <div class="row mb-5 align-items-center">
            <div class="col-lg-8 mb-3 mb-lg-0">
                <h4 class="info-title mb-3">Antecedentes Históricos</h4>
                <p class="info-text">
                    El departamento de Cartografía es el encargado de llevar a cabo la administración y actualización del Sistema de Información Geográfica Municipal (SIGMUN), contiene información geográfica de infraestructura, equipamiento municipal, crecimiento histórico, entre otros. Asimismo sirve como herramienta e insumo para los diferentes órdenes de gobierno, el Instituto y público en general, a partir de la cual se generan mapas temáticos. El sistema contiene elementos urbanos en capas de información geográfica.
                </p>
                <p class="info-text">
                    Este esfuerzo comenzó en 1993 y una parte primordial para lograr lo que hoy tenemos, fue la creación de un Sistema Municipal de Información Documental (SIMID) cuyo objetivo fue documentar el manejo de la información para proveer una herramienta confiable en la toma de decisiones a directores de dependencias municipales y ser la base para el actual Sistema de Información Geográfica Municipal (SIGMUN).
                </p>
            </div>
            <div class="col-lg-4 text-center">
                <div class="info-section-image-link mx-auto" style="max-width: 90%;">
                    <img src="{{ asset('imagenes/cartografia_crec_urbano.jpg') }}" alt="Antecedentes Cartografía" class="img-fluid rounded shadow-sm">
                </div>
            </div>
        </div>

        <div class="row mb-5 align-items-center">
            <div class="col-lg-4 text-center mb-3 mb-lg-0">
                <a href="https://sigimip.org.mx/" target="_blank" class="info-section-image-link mx-auto" style="max-width: 90%;">
                    <img src="{{ asset('imagenes/cartogde.png') }}" alt="SIGMUN" class="img-fluid rounded shadow-sm">
                    <i class="bi bi-box-arrow-up-right image-overlay-icon"></i>
                </a>
            </div>
            <div class="col-lg-8">
                <h4 class="info-title mb-3">¿Qué puedes hacer en SIGMUN?</h4>
                <p class="info-text">
                    SIGMUN es una plataforma de información geográfica que permite la consulta, análisis y visualización de datos espaciales mediante mapas interactivos. Los usuarios pueden explorar información temática a través de filtros espaciales y temporales, acceder a un repositorio centralizado de aplicaciones y mapas especializados, así como descargar información geoespacial en formato vectorial para su integración y análisis en software SIG especializado.
                </p>
                <div class="mt-4">
                    <a href="https://sigimip.org.mx/" target="_blank" class="btn btn-sigmun">
                        <i class="bi bi-box-arrow-up-right me-2"></i>Ir al sitio
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection