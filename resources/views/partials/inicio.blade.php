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
</style>

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
                        <div class="module-image-container mb-3" onclick="SIGEMApp.loadContent('catalogo')">
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
                        <button class="btn btn-success btn-sm" onclick="SIGEMApp.loadContent('catalogo')">
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
                        <div class="module-image-container mb-3" onclick="SIGEMApp.loadContent('estadistica')">
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
                        <button class="btn btn-success btn-sm" onclick="SIGEMApp.loadContent('estadistica')">
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
                        <div class="module-image-container mb-3" onclick="SIGEMApp.loadContent('cartografia')">
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
                        <button class="btn btn-success btn-sm" onclick="SIGEMApp.loadContent('cartografia')">
                            <i class="bi bi-arrow-right me-1"></i>Ver Mapas
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="bi bi-lightning-fill me-2"></i>Consulta Express</h5>
    </div>
    <div class="card-body">
        <div class="container" id="consulta-express-container">
            <div class="row">
                <!-- Columna de imagen -->
                <div class="col-md-3 text-center mb-3 mb-md-0">
                    <img src="{{ asset('imagenes/express.png') }}" alt="Consulta Express" class="img-fluid rounded shadow-sm" style="max-height: 220px;">
                </div>
                
                <!-- Columna de selectores -->
                <div class="col-md-3">
                    <div class="form-group mb-3">
                        <label for="ce_tema_select" class="form-label">Tema:</label>
                        <select id="ce_tema_select" class="form-select">
                            <option value="">Seleccione un tema...</option>
                            <!-- Los temas se cargarán dinámicamente -->
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="ce_subtema_select" class="form-label">Subtema:</label>
                        <select id="ce_subtema_select" class="form-select" disabled>
                            <option value="">Primero seleccione un tema</option>
                            <!-- Los subtemas se cargarán dinámicamente -->
                        </select>
                    </div>
                    <button id="ce_consultar_btn" class="btn btn-primary w-100" disabled>
                        Consultar <i class="bi bi-arrow-right-circle ms-1"></i>
                    </button>
                </div>
                
                <!-- Columna de contenido -->
                <div class="col-md-6">
                    <div id="ce_contenido_container" class="border rounded p-3" style="min-height: 250px; max-height: 500px; overflow-y: auto;">
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-info-circle fs-2"></i>
                            <p class="mt-2">Seleccione un tema y subtema para ver la información</p>
                        </div>
                    </div>
                    <div id="ce_metadata" class="text-end text-muted small mt-2" style="display: none;">
                        Última actualización: <span id="ce_fecha_actualizacion">-</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Cargar temas al inicio
    cargarTemasConsultaExpress();
    
    // Evento de cambio en el selector de temas
    $('#ce_tema_select').on('change', function() {
        const temaId = $(this).val();
        if (temaId) {
            cargarSubtemasConsultaExpress(temaId);
            $('#ce_subtema_select').prop('disabled', false);
        } else {
            $('#ce_subtema_select').html('<option value="">Primero seleccione un tema</option>');
            $('#ce_subtema_select').prop('disabled', true);
            $('#ce_consultar_btn').prop('disabled', true);
        }
    });
    
    // Evento de cambio en el selector de subtemas
    $('#ce_subtema_select').on('change', function() {
        const subtemaId = $(this).val();
        if (subtemaId) {
            $('#ce_consultar_btn').prop('disabled', false);
            // Carga automática de contenido al seleccionar subtema (opcional)
            // cargarContenidoConsultaExpress(subtemaId);
        } else {
            $('#ce_consultar_btn').prop('disabled', true);
        }
    });
    
    // Evento clic en botón consultar
    $('#ce_consultar_btn').on('click', function() {
        const subtemaId = $('#ce_subtema_select').val();
        if (subtemaId) {
            cargarContenidoConsultaExpress(subtemaId);
        }
    });
    
    // Función para cargar los temas
    function cargarTemasConsultaExpress() {
        $.ajax({
            url: '{{ route("sigem.consulta-express.temas") }}',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success && response.temas.length > 0) {
                    let options = '<option value="">Seleccione un tema...</option>';
                    response.temas.forEach(function(tema) {
                        options += `<option value="${tema.ce_tema_id}">${tema.tema}</option>`;
                    });
                    $('#ce_tema_select').html(options);
                } else {
                    console.error('No se encontraron temas:', response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar temas:', error);
            }
        });
    }
    
    // Función para cargar los subtemas de un tema
    function cargarSubtemasConsultaExpress(temaId) {
        const url = '{{ route("sigem.consulta-express.temas") }}';
        
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const tema = response.temas.find(t => t.ce_tema_id == temaId);
                    if (tema && tema.subtemas.length > 0) {
                        let options = '<option value="">Seleccione un subtema...</option>';
                        tema.subtemas.forEach(function(subtema) {
                            options += `<option value="${subtema.ce_subtema_id}">${subtema.ce_subtema}</option>`;
                        });
                        $('#ce_subtema_select').html(options);
                    } else {
                        $('#ce_subtema_select').html('<option value="">No hay subtemas disponibles</option>');
                    }
                } else {
                    console.error('Error al cargar subtemas:', response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar subtemas:', error);
            }
        });
    }
    
    // Función para cargar el contenido de un subtema
    function cargarContenidoConsultaExpress(subtemaId) {
        $('#ce_contenido_container').html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div><p class="mt-2">Cargando contenido...</p></div>');
        
        $.ajax({
            url: `{{ url('sigem/consulta-express/contenido') }}/${subtemaId}`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success && response.contenido) {
                    $('#ce_contenido_container').html(response.contenido.ce_contenido);
                    $('#ce_fecha_actualizacion').text(response.actualizado);
                    $('#ce_metadata').show();
                } else {
                    $('#ce_contenido_container').html('<div class="alert alert-warning">No se encontró contenido para el subtema seleccionado.</div>');
                    $('#ce_metadata').hide();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar contenido:', error);
                $('#ce_contenido_container').html('<div class="alert alert-danger">Error al cargar el contenido. Por favor intente nuevamente.</div>');
                $('#ce_metadata').hide();
            }
        });
    }
});
</script>
