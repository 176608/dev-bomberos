@extends('VisorSIGEM.layouts.visor')

@section('visor_title', 'SIGEM v2 — Productos')

@section('visor_content')
<style>
.product-card {
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

.product-section {
    display: flex;
    align-items: flex-start;
    gap: 20px;
}

.product-image-link {
    flex-shrink: 0;
    display: block;
    text-decoration: none;
    width: 200px;
}

.product-image-container {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    background: #f8f9fa;
}

.product-image-container img {
    width: 100%;
    height: auto;
    display: block;
    transition: all 0.3s ease;
}

.product-image-link:hover .product-image-container img {
    transform: scale(1.05);
}

.product-image-link:hover .product-image-container {
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.product-overlay {
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
    font-size: 2rem;
}

.product-image-link:hover .product-overlay {
    opacity: 1;
}

.product-image-link:hover .product-overlay i {
    animation: bounce 0.6s ease infinite alternate;
}

@keyframes bounce {
    0% { transform: scale(1); }
    100% { transform: scale(1.1); }
}

.product-text {
    flex: 1;
}

.product-text h5 a:hover {
    color: #198754 !important;
}

.product-image-container-static {
    position: relative;
    width: 200px;
    flex-shrink: 0;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    background: #f8f9fa;
    opacity: 0.75;
    filter: grayscale(0.3);
    transition: all 0.3s ease;
}

.product-image-container-static img {
    width: 100%;
    height: auto;
    display: block;
}

.product-unavailable-badge {
    display: inline-block;
    background: #6c757d;
    color: white;
    font-size: 0.75rem;
    padding: 2px 10px;
    border-radius: 20px;
    font-weight: 600;
    letter-spacing: 0.3px;
    margin-top: 4px;
}

.product-text-muted {
    opacity: 0.8;
}

.product-text-muted h5 {
    color: #6c757d !important;
    cursor: default;
}

@media (max-width: 768px) {
    .product-section {
        flex-direction: column;
        text-align: center;
        align-items: center;
    }
    .product-image-link,
    .product-image-container-static {
        width: 100%;
        max-width: 250px;
    }
}
</style>

<div class="card shadow-sm">
    <div class="card-body">
        <h2 class="text-success mb-4 text-center">
            <i class="bi bi-box-seam-fill"></i> Productos Cartográficos y Estadísticos
        </h2>

        <div class="row g-4">

            <div class="col-12">
                <div class="card h-100 product-card">
                    <div class="card-body">
                        <div class="product-section">
                            <a href="https://www.imip.org.mx/imip/files/publicaciones/Radiografia_2026.pdf" target="_blank" class="product-image-link">
                                <div class="product-image-container">
                                    <img src="{{ asset('imagenes/RadiografiaSocioeconomica2025.png') }}" alt="Radiografía Socioeconómica" class="img-fluid">
                                    <div class="product-overlay">
                                        <i class="bi bi-box-arrow-up-right"></i>
                                    </div>
                                </div>
                            </a>
                            <div class="product-text">
                                <h5>
                                    <a href="https://www.imip.org.mx/imip/files/publicaciones/Radiografia_2026.pdf" target="_blank" class="text-decoration-none">
                                        <i class="bi bi-file-earmark-text me-2"></i>
                                        Ultima Radiografía Socioeconómica del Municipio de Juárez 2025
                                        <i class="bi bi-box-arrow-up-right ms-1"></i>
                                    </a>
                                </h5>
                                <p>En esta edición, se ha puesto especial atención en reflejar los cambios, retos y avances que marcaron el cierre de 2025 y el inicio de 2026, entendiendo que detrás de cada indicador existe una realidad social que invita a la reflexión, la toma de decisiones y la construcción de una ciudad más resiliente, competitiva y equitativa para todas y todos.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card h-100 product-card">
                    <div class="card-body">
                        <div class="product-section">
                            <a href="https://www.imip.org.mx/imip/node/41" target="_blank" class="product-image-link">
                                <div class="product-image-container">
                                    <img src="{{ asset('imagenes/rad2020.png') }}" alt="Radiografía Socioeconómica" class="img-fluid">
                                    <div class="product-overlay">
                                        <i class="bi bi-box-arrow-up-right"></i>
                                    </div>
                                </div>
                            </a>
                            <div class="product-text">
                                <h5>
                                    <a href="https://www.imip.org.mx/imip/node/41" target="_blank" class="text-decoration-none">
                                        <i class="bi bi-folder2"></i>
                                        Repositorio de Radiografías Socioeconómicas del Municipio de Juárez
                                        <i class="bi bi-box-arrow-up-right ms-1"></i>
                                    </a>
                                </h5>
                                <p>Recopilación de radiografías socioeconómicas, un documento que se ha convertido en una herramienta de referencia y consulta en cuanto a las diversas características socioeconómicas del municipio. Ofrece datos sobre los principales temas de interés para la toma de decisiones del sector público como privado de la región, así como de apoyo a los estudiantes y población en general.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-12">
                <div class="card h-100 product-card">
                    <div class="card-body">
                        <div class="product-section">
                            <a href="https://www.imip.org.mx/imip/node/360" target="_blank" class="product-image-link">
                                <div class="product-image-container">
                                    <img src="{{ asset('imagenes/Encuesta_Amigabilidad_0.jpg') }}" alt="Encuesta" class="img-fluid">
                                    <div class="product-overlay">
                                        <i class="bi bi-box-arrow-up-right"></i>
                                    </div>
                                </div>
                            </a>
                            <div class="product-text">
                                <h5>
                                    <a href="https://www.imip.org.mx/imip/node/360" target="_blank" class="text-decoration-none">
                                        <i class="bi bi-clipboard-data"></i>
                                        Resultados de Encuesta: “Juárez en el camino de la amigabilidad hacia las personas mayores”
                                        <i class="bi bi-box-arrow-up-right ms-1"></i>
                                    </a>
                                </h5>
                                <p>El Instituto Municipal de Investigación y Planeación (IMIP) en conjunto con el Desarrollo Integral de la Familia (DIF) del municipio de Juárez, presentan los resultados de la encuesta de percepción sobre las condiciones de amigabilidad urbana en Ciudad Juárez, con enfoque en la población de 60 años y más.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-12">
                <div class="card h-100 product-card">
                    <div class="card-body">
                        <div class="product-section">
                            <a href="https://www.imip.org.mx/m_aux/public/biblioteca" target="_blank" class="product-image-link">
                                <div class="product-image-container">
                                    <img src="{{ asset('imagenes/abigail.jpeg') }}" alt="Biblioteca" class="img-fluid">
                                    <div class="product-overlay">
                                        <i class="bi bi-box-arrow-up-right"></i>
                                    </div>
                                </div>
                            </a>
                            <div class="product-text">
                                <h5>
                                    <a href="https://www.imip.org.mx/m_aux/public/biblioteca" target="_blank" class="text-decoration-none">
                                        <i class="bi bi-book me-2"></i>
                                        Biblioteca MPDU: Abigail García Espinosa
                                        <i class="bi bi-box-arrow-up-right ms-1"></i>
                                    </a>
                                </h5>
                                <p>Cuenta con un amplio acervo documental y bancos de datos especializados. Ideal para investigaciones urbanas, tesis, proyectos y trabajos académicos. Forma parte de la Red de Consulta del INEGI.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card h-100 product-card">
                    <div class="card-body">
                        <div class="product-section">
                            <div class="product-image-container-static">
                                <img src="{{ asset('imagenes/PoratadaCARTO.png') }}" alt="Cartografía 2019" class="img-fluid">
                            </div>
                            <div class="product-text product-text-muted">
                                <h5 class="text-secondary">
                                    <i class="bi bi-map me-2"></i>
                                    Cuaderno de Información Cartográfica
                                    <span class="product-unavailable-badge">
                                        <i class="bi bi-lock me-1"></i>Solo impreso
                                    </span>
                                </h5>
                                <p class="mb-2">Es una guía de información confiable y actualizada, compuesta por mapas con índice de calles, colonias y capas temáticas como escuelas, hospitales, estaciones, museos, teatros, unidades deportivas, hoteles, cines, entre otros. Disponible unicamente en formato impreso.</p>
                                <small class="text-muted fst-italic">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Este producto no está disponible en formato digital. Consulte la versión impresa en las oficinas del IMIP.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card h-100 product-card">
                    <div class="card-body">
                        <div class="product-section">
                            <a href="https://www.imip.org.mx/directorio/" target="_blank" class="product-image-link">
                                <div class="product-image-container">
                                    <img src="{{ asset('imagenes/general.png') }}" alt="Directorio 2014" class="img-fluid">
                                    <div class="product-overlay">
                                        <i class="bi bi-box-arrow-up-right"></i>
                                    </div>
                                </div>
                            </a>
                            <div class="product-text">
                                <h5>
                                    <a href="https://www.imip.org.mx/directorio/" target="_blank" class="text-decoration-none">
                                        <i class="bi bi-building me-2"></i>
                                        Directorio Georreferenciado de Parques, Zonas Industriales e Industrias en Ciudad Juárez, 2014
                                        <i class="bi bi-box-arrow-up-right ms-1"></i>
                                    </a>
                                </h5>
                                <p>Incluye información estadística y geográfica de empresas manufactureras en la ciudad, clasificadas por tamaño y actividad. Contiene datos de empresas dentro y fuera de parques industriales.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="alert alert-success text-center mt-4">
            <i class="bi bi-info-circle-fill me-2"></i>
            Encuentra además otros productos estadísticos y cartográficos en
            <a href="https://www.imip.org.mx/imip/publicaciones-en-linea" target="_blank" class="alert-link fw-bold">
                la página web del Instituto Municipal de Investigación y Planeación <i class="bi bi-box-arrow-up-right"></i>
            </a>
        </div>
    </div>
</div>
@endsection