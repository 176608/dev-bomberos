<div class="card shadow-sm">
    <div class="card-body p-0">
        <!-- VISTA DE TEMA SELECCIONADO CON SUBTEMAS LATERALES -->
        <div class="row g-0 min-vh-75">
            <!-- SIDEBAR PARA SUBTEMAS - Con ID para manipularlo con JS -->
            <div class="col-md-4 bg-light border-end transition-width" id="sidebar-subtemas">
                <div class="d-flex flex-column h-100">
                    <!-- Header del Sidebar -->
                    <div class="p-3 text-white position-relative" style="background-color: #0b584fff;">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 sidebar-title">
                                <i class="bi bi-list-ul me-2"></i>Subtemas de {{ $tema_seleccionado->tema_titulo }}
                            </h6>
                        </div>
                        
                        <!-- Botón toggle con mejor posicionamiento -->
                        <div class="toggle-button-container">
                            <button class="btn-toggle-sidebar-fixed" id="toggle-sidebar" title="Expandir/Colapsar panel">
                                <i class="bi bi-chevron-left"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Navegación de Subtemas -->
                    <div class="flex-fill overflow-auto sidebar-content" id="subtemas-navegacion">
                        @if($tema_subtemas && $tema_subtemas->count() > 0)
                            @foreach($tema_subtemas as $tema_subtema)
                                <a href="javascript:void(0)" 
                                   onclick="cargarSubtema({{ $tema_subtema->subtema_id }}); return false;" 
                                   class="subtema-nav-item text-decoration-none text-dark {{ isset($subtema_seleccionado) && $tema_subtema->subtema_id == $subtema_seleccionado->subtema_id ? 'active' : '' }}"
                                >
                                    <div class="row g-0 w-100 align-items-center">
                                        <div class="col-3 subtema-image-container">
                                            @if($tema_subtema->imagen)
                                                <img src="{{ asset('imagenes/subtemas_u/'.$tema_subtema->imagen) }}" 
                                                     alt="{{ $tema_subtema->subtema_titulo }}" 
                                                     class="subtema-image img-fluid">
                                            @else
                                                <div class="no-image-placeholder d-flex align-items-center justify-content-center">
                                                    <i class="bi bi-collection text-success fs-3"></i>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div class="col-9 subtema-texto">
                                            <div class="d-flex align-items-center justify-content-between w-100">
                                                <h6 class="mb-1">{{ $tema_subtema->subtema_titulo }}</h6>
                                                <i class="bi bi-chevron-right ms-2"></i>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        @else
                            <div class="p-3 text-center text-muted">
                                <i class="bi bi-folder-x" style="font-size: 2rem;"></i>
                                <p class="mt-2 mb-0">No hay subtemas disponibles</p>
                                <a href="{{ url('/sigem?section=estadistica') }}" class="btn btn-outline-secondary btn-sm mt-3">
                                    <i class="bi bi-arrow-left me-1"></i>Volver a temas estadísticos
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- CONTENIDO DE CUADROS ESTADÍSTICOS -->
            <div class="col-md-8 transition-width" id="contenido-principal">
                <div class="d-flex flex-column h-100">
                    <!-- Encabezado con selector de temas y botón para mostrar sidebar -->
                    <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <!-- Botón para mostrar sidebar (visible solo cuando está colapsado) -->
                            <button class="btn btn-sm btn-outline-success me-3 d-none" id="show-sidebar" title="Mostrar panel de subtemas">
                                <i class="bi bi-list"></i>
                            </button>
                            
                            <!-- Selector de Temas -->
                            <div style="min-width: 250px;">
                                <select class="form-select" id="tema-selector" onchange="cambiarTema(this.value)">
                                    @foreach($temas as $tema)
                                        <option value="{{ $tema->tema_id }}" {{ $tema_seleccionado->tema_id == $tema->tema_id ? 'selected' : '' }}>
                                            {{ $tema->orden_indice }}. {{ $tema->tema_titulo }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <!-- Enlace para volver a la vista de temas -->
                        <a href="{{ url('/sigem?section=estadistica') }}" class="btn btn-sm btn-outline-secondary d-none d-md-inline">
                            <i class="bi bi-arrow-left me-1"></i>Volver a temas
                        </a>
                    </div>

                    <!-- Encabezado de subtema seleccionado -->
                    <div class="p-3 border-bottom" id="subtema-header">
                        @if(isset($subtema_seleccionado))
                            <h5 class="mb-0">{{ $subtema_seleccionado->subtema_titulo }}</h5>
                        @else
                            <h5 class="mb-0">{{ $tema_seleccionado->tema_titulo }}</h5>
                            <p class="text-muted small mb-0">Seleccione un subtema para ver sus cuadros</p>
                        @endif
                    </div>

                    <!-- Lista de cuadros -->
                    <div class="flex-fill overflow-auto p-3" id="cuadros-container-estadistica">
                        @php
                            // Función para extraer el número del índice de un código de cuadro
                            function extraerNumeroIndice($codigoCuadro) {
                                // Verificar si el código está vacío
                                if (empty($codigoCuadro)) {
                                    return PHP_FLOAT_MAX; // Valor alto para que queden al final
                                }
                                
                                // Patrón para extraer el número del índice (último componente después del punto)
                                if (preg_match('/\.(\d+(?:\.\d+)*)$/', $codigoCuadro, $matches)) {
                                    return floatval($matches[1]); // Convertir a número para ordenamiento correcto
                                } else if (preg_match('/(\d+(?:\.\d+)*)$/', $codigoCuadro, $matches)) {
                                    // Si no hay punto pero termina en número
                                    return floatval($matches[1]);
                                }
                                
                                return PHP_FLOAT_MAX; // Por defecto al final
                            }
                            
                            // Ordenar cuadros por su número de índice
                            if (isset($cuadros) && $cuadros->count() > 0) {
                                $cuadrosArray = $cuadros->toArray();
                                usort($cuadrosArray, function($a, $b) {
                                    $numA = extraerNumeroIndice($a['codigo_cuadro'] ?? '');
                                    $numB = extraerNumeroIndice($b['codigo_cuadro'] ?? '');
                                    return $numA <=> $numB;
                                });
                                $cuadros = collect($cuadrosArray);
                            }
                        @endphp

                        @if(isset($cuadros) && $cuadros->count() > 0 && isset($subtema_seleccionado))
                            <div class="cuadros-lista">
                                @foreach($cuadros as $cuadro)
                                    <a href="javascript:void(0)" 
                                       onclick="SIGEMApp.verCuadro('{{ $cuadro['cuadro_estadistico_id'] }}', '{{ $cuadro['codigo_cuadro'] }}')" 
                                       class="cuadro-item p-3 mb-3 border rounded text-decoration-none d-block">
                                        <div class="row align-items-center">
                                            <!-- Columna 1: Iconos y datos rápidos -->
                                            <div class="col-2 mb-2 mb-md-0">
                                                @if(!empty($cuadro['tipo_mapa_pdf']) && $cuadro['tipo_mapa_pdf'])
                                                    <span class="text-secondary me-2" title="Mapa Asignado a cuadro"><i class="bi bi-map-fill"></i> &nbsp; Mapa PDF</span>
                                                @else
                                                    @if(!empty($cuadro['excel_file']))
                                                        <span class="text-primary me-2" title="Dataset asignado a cuadro"><i class="bi bi-table"></i></span>
                                                    @endif
                                                    @if(!empty($cuadro['excel_formated_file']))
                                                        <span class="text-success me-2" title="Excel formateado asignado a cuadro"><i class="bi bi-file-earmark-excel"></i></span>
                                                    @endif
                                                @endif

                                                @if(!empty($cuadro['pdf_file']))
                                                    <span class="text-danger me-2" title="PDF asignado a cuadro"><i class="bi bi-file-earmark-pdf"></i></span>
                                                @endif

                                                @if(!empty($cuadro['permite_grafica']))
                                                    <span class="badge bg-info" title="Permite gráficas">
                                                        <i class="bi bi-graph-up"></i>
                                                    </span>
                                                @endif
                                            </div>
                                            <!-- Columna 2: Título y subtítulo -->
                                            <div class="col-10">
                                                <span class="mb-1 d-block text-dark">
                                                    <span class="fw-bold text-success">
                                                        {{ $cuadro['codigo_cuadro'] ?? 'N/A' }}
                                                    </span>
                                                    {{ $cuadro['cuadro_estadistico_titulo'] ?? 'Sin título' }}
                                                </span>
                                                @if(isset($cuadro['cuadro_estadistico_subtitulo']) && !empty($cuadro['cuadro_estadistico_subtitulo']))
                                                    <small class="text-muted d-block">{{ $cuadro['cuadro_estadistico_subtitulo'] }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="bi bi-table" style="font-size: 3rem;"></i>
                                <p class="mt-3">Seleccione un subtema para ver los cuadros estadísticos disponibles.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="{{ asset('css/temas_subtemas.css') }}">
<!-- Cargar el motor de Excel -->
<script src="{{ asset('js/excel_in_modal_eng.js') }}"></script>
<script src="{{ asset('js/grafica_in_modal_eng.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar manejo del sidebar
    initSidebarToggle();
    
    // Precargar SheetJS para uso posterior
    if (typeof XLSX === 'undefined') {
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js';
        script.async = true;
        document.head.appendChild(script);
    }
});

// Función para inicializar el toggle del sidebar
function initSidebarToggle() {
    const sidebar = document.getElementById('sidebar-subtemas');
    const content = document.getElementById('contenido-principal');
    const toggleBtn = document.getElementById('toggle-sidebar');
    
    // CAMBIO PRINCIPAL: Verificar si hay preferencia guardada, pero por defecto colapsada
    const sidebarCollapsed = localStorage.getItem('subtemas-sidebar-collapsed');
    
    // Aplicar estado inicial - COLAPSADA POR DEFECTO
    if (sidebarCollapsed === null || sidebarCollapsed === 'true') {
        // Primera visita o preferencia colapsada
        collapseSidebar();
    } else if (sidebarCollapsed === 'false') {
        // Solo expandir si explícitamente el usuario lo prefiere expandida
        expandSidebar();
    }
    
    // Manejar clic en botón toggle
    toggleBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        if (sidebar.classList.contains('sidebar-mini')) {
            expandSidebar();
        } else {
            collapseSidebar();
        }
    });
    
    // Función para colapsar sidebar
    function collapseSidebar() {
        sidebar.classList.add('sidebar-mini');
        content.classList.add('content-expanded');
        localStorage.setItem('subtemas-sidebar-collapsed', 'true');
        toggleBtn.setAttribute('title', 'Expandir panel');
        
        // Agregar tooltip a los elementos cuando está colapsado
        document.querySelectorAll('.subtema-nav-item').forEach(item => {
            const subtemaTitle = item.querySelector('.subtema-texto h6')?.innerText || 'Subtema';
            item.setAttribute('title', subtemaTitle);
            
            // Bootstrap tooltip si está disponible
            if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                new bootstrap.Tooltip(item, {
                    placement: 'right',
                    trigger: 'hover',
                    container: 'body'
                });
            }
        });
    }
    
    // Función para expandir sidebar
    function expandSidebar() {
        sidebar.classList.remove('sidebar-mini');
        content.classList.remove('content-expanded');
        localStorage.setItem('subtemas-sidebar-collapsed', 'false');
        toggleBtn.setAttribute('title', 'Colapsar panel');
        
        // Eliminar tooltips
        document.querySelectorAll('.subtema-nav-item').forEach(item => {
            item.removeAttribute('title');
            
            // Destruir tooltip de Bootstrap si existe
            if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                const tooltip = bootstrap.Tooltip.getInstance(item);
                if (tooltip) {
                    tooltip.dispose();
                }
            }
        });
    }
}

// Función para cambiar de tema
function cambiarTema(tema_id) {
    //console.log('Cambiando a tema:', tema_id);
    window.location.href = '{{ url("/sigem/estadistica-por-tema") }}/' + tema_id;
}

// Función para cargar un subtema específico
function cargarSubtema(subtema_id) {
    // Mostrar indicador de carga
    document.getElementById('cuadros-container-estadistica').innerHTML = `
        <div class="text-center p-5">
            <div class="spinner-border text-success" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-3">Cargando cuadros estadísticos...</p>
        </div>
    `;
    
    // Actualizar clase activa en la navegación de subtemas
    document.querySelectorAll('#subtemas-navegacion .subtema-nav-item').forEach(item => {
        item.classList.remove('active');
    });
    event.currentTarget.classList.add('active');
    
    // 1. PRIMERO actualizar el encabezado (para respuesta inmediata)
    const subtemaSeleccionado = event.currentTarget.querySelector('.subtema-texto h6')?.innerText || 'Subtema';
    //const temaSeleccionado = document.querySelector('#tema-selector option:checked')?.text || 'Tema';
    
    // Actualizar de forma preliminar mientras se carga la información completa
    const headerContainer = document.getElementById('subtema-header');
    if (headerContainer) {
        headerContainer.innerHTML = `
            <h5 class="mb-0">${subtemaSeleccionado}</h5>
        `;
    }
    
    // 2. LUEGO cargar los cuadros del subtema mediante AJAX
    fetch('{{ url("/sigem/obtener-cuadros-estadistica") }}/' + subtema_id)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                actualizarEncabezadoSubtema(subtema_id);
                
                // Renderizar cuadros
                renderizarCuadros(data.cuadros);
            } else {
                document.getElementById('cuadros-container-estadistica').innerHTML = `
                    <div class="alert alert-danger m-3">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        ${data.message || 'Error al cargar cuadros estadísticos'}
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error al cargar subtema:', error);
            document.getElementById('cuadros-container-estadistica').innerHTML = `
                <div class="alert alert-danger m-3">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Error de conexión al cargar cuadros estadísticos
                </div>
            `;
        });
}

function actualizarEncabezadoSubtema(subtema_id) {
    fetch('{{ url("/sigem/obtener-info-subtema") }}/' + subtema_id)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const headerContainer = document.getElementById('subtema-header');
                headerContainer.innerHTML = `
                    <h5 class="mb-0">${data.subtema.subtema_titulo}</h5>
                    <p class="act15 text-muted small mb-0">${data.subtema.tema ? data.subtema.tema.tema_titulo : ''}</p>
                `;
            }
        })
        .catch(error => console.error('Error al obtener info del subtema:', error));
}

function renderizarCuadros(cuadros) {
    const container = document.getElementById('cuadros-container-estadistica');
    
    if (!cuadros || cuadros.length === 0) {
        container.innerHTML = `
            <div class="text-center text-muted py-5">
                <i class="bi bi-table" style="font-size: 3rem;"></i>
                <p class="mt-3">No hay cuadros estadísticos disponibles para este subtema.</p>
            </div>
        `;
        return;
    }
    
    function extraerNumeroIndice(codigoCuadro) {
        if (!codigoCuadro) {
            return Number.MAX_VALUE; 
        }
        
        const match = codigoCuadro.match(/\.(\d+(?:\.\d+)*)$/);
        if (match) {
            return parseFloat(match[1]); 
        } else {
            const matchEnd = codigoCuadro.match(/(\d+(?:\.\d+)*)$/);
            if (matchEnd) {
               
                return parseFloat(matchEnd[1]);
            }
        }
        
        return Number.MAX_VALUE; 
    }
    

    const cuadrosOrdenados = [...cuadros].sort((a, b) => {
        const numA = extraerNumeroIndice(a.codigo_cuadro || '');
        const numB = extraerNumeroIndice(b.codigo_cuadro || '');
        return numA - numB;
    });

    
    let html = '<div class="cuadros-lista">';
    
    cuadrosOrdenados.forEach(cuadro => {
        html += `
            <a href="javascript:void(0)" 
               onclick="SIGEMApp.verCuadro('${cuadro.cuadro_estadistico_id}', '${cuadro.codigo_cuadro || ''}')" 
               class="cuadro-item p-3 mb-3 border rounded text-decoration-none d-block">
                <div class="row align-items-center">
                    <div class="col-2 mb-2 mb-md-0">
                        ${cuadro.tipo_mapa_pdf ? `<span class="text-secondary me-2" title="Mapa Asignado a cuadro"><i class="bi bi-map-fill"></i> &nbsp; Mapa PDF</span>` : (cuadro.excel_file && cuadro.excel_file !== '' ? `<span class="text-primary me-2" title="Dataset asignado a cuadro"><i class="bi bi-table"></i></span>` : '')}
                        ${cuadro.pdf_file && cuadro.pdf_file !== '' ? `<span class="text-danger me-2" title="PDF asignado a cuadro"><i class="bi bi-file-earmark-pdf"></i></span>` : ''}
                        ${(!cuadro.tipo_mapa_pdf && cuadro.excel_formated_file && cuadro.excel_formated_file !== '') ? `<span class="text-success me-2" title="Excel formateado asignado a cuadro"><i class="bi bi-file-earmark-excel"></i></span>` : ''}
                        ${cuadro.permite_grafica ? `<span class="badge bg-info" title="Permite gráficas"><i class="bi bi-graph-up"></i></span>` : ''}
                    </div>
                    <div class="col-10">
                        <span class="mb-1 d-block text-dark">
                            <span class="fw-bold text-success">
                                ${cuadro.codigo_cuadro || 'N/A'}
                            </span>
                            ${cuadro.cuadro_estadistico_titulo || 'Sin título'}
                        </span>
                        ${cuadro.cuadro_estadistico_subtitulo
                            ? `<small class="text-muted d-block">${cuadro.cuadro_estadistico_subtitulo}</small>`
                            : ''
                        }
                    </div>
                </div>
            </a>
        `;
    });
    
    html += '</div>';
    container.innerHTML = html;
}

document.addEventListener('verCuadroEstadistico', function(event) {
    const { cuadroId, codigo } = event.detail;
    mostrarModalCuadro(cuadroId, codigo);
});

let isModalOpen = false;

function mostrarModalCuadro(cuadroId, codigo) {
if (isModalOpen) {
        return;
    }
    isModalOpen = true;

    fetch(`{{ url('/sigem/obtener-archivos-cuadro') }}/${cuadroId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status} ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            
            if (!data.success) {
                throw new Error(data.message || 'Error al obtener información del cuadro');
            }
            
            const cuadro = data.cuadro;
            //console.log('BLADE: Información del cuadro:', cuadro);
            
            const tieneExcel = data.excel.tiene_archivo && data.excel.archivo_existe;
            const excelUrl = data.excel.url;
            const tienePdf = data.pdf.tiene_archivo && data.pdf.archivo_existe;
            const pdfUrl = data.pdf.url;
            const tieneFormatedExcel = data.excel_formated.tiene_archivo && data.excel_formated.archivo_existe;
            const formatedExcelUrl = data.excel_formated.url;

            const modalId = `modal_excel_${Date.now()}`;
            const isMapaPdf = (cuadro.tipo_mapa_pdf == 1 || cuadro.tipo_mapa_pdf === true);

            let modalHTML = '';
            if (isMapaPdf) {
                // Modal simplificado para mostrar solo el PDF asociado
                modalHTML = `
                <div class="modal fade" id="${modalId}" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header bg-success text-white">
                                <h5 class="modal-title">
                                    <span class="fw-bold">${cuadro.codigo}:</span>
                                    <span class="fw-light">${cuadro.titulo || ''}</span>
                                    ${cuadro.subtitulo ? `<small class="text-white fst-italic ms-2">${cuadro.subtitulo}</small>` : ''}
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                            </div>
                            <div class="modal-body p-0">
                                <div class="p-3">
                                    ${ pdfUrl ? `
                                        <object data="${pdfUrl}" type="application/pdf" width="100%" height="680px">
                                            <p class="text-center">Su navegador no puede mostrar PDFs inline. <a href="${pdfUrl}" target="_blank">Abrir en nueva pestaña</a></p>
                                        </object>
                                    ` : `
                                        <div class="alert alert-warning text-center">
                                            <i class="bi bi-exclamation-triangle me-2"></i>
                                            No hay PDF asociado a este cuadro.
                                        </div>
                                    ` }
                                </div>
                            </div>
                            <div class="modal-footer">
                                <div class="d-flex justify-content-center w-100">
                                    ${ pdfUrl ? `<a href="${pdfUrl}" class="btn btn-outline-success me-2" download><i class="bi bi-download"></i> Descargar PDF</a>` : '' }
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                `;
            } else {
                // Modal original: Excel + gráfica
                modalHTML = `
                <div class="modal fade" id="${modalId}" tabindex="-1" aria-labelledby="excelModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header bg-success text-white">
                                <h5 class="modal-title">
                                    <span class="fw-bold">${cuadro.codigo}:</span>
                                    <span class="fw-light">${cuadro.titulo || ''}</span>
                                    ${cuadro.subtitulo ? `<small class="text-white fst-italic ms-2">${cuadro.subtitulo}</small>` : ''}
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                            </div>

                            <div class="modal-body p-0">

                                <div id="excel-view-${modalId}">
                                    <div id="excel-container-${modalId}" class="p-3">
                                        <div class="text-center py-5">
                                            <div class="spinner-border text-success" role="status">
                                                <span class="visually-hidden">Cargando...</span>
                                            </div>
                                            <p class="mt-3">Cargando archivo Excel...</p>
                                        </div>
                                    </div>
                                    <div id="excel-info-${modalId}" class="p-3">
                                        <div class="text-center">${cuadro.pie_pagina}</div>
                                    </div>
                                </div>

                                <div id="grafica-view-${modalId}" style="display:none;">
                                    <div id="grafica-container-${modalId}" class="p-3">
                                        <!-- Aquí se renderizará la gráfica -->
                                        <div class="text-center py-5">
                                            <div class="spinner-border text-success" role="status">
                                                <span class="visually-hidden">Cargando...</span>
                                            </div>
                                            <p class="mt-3">Cargando gráfica...</p>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="modal-footer">
                                <div class="d-flex justify-content-center w-100">
                                    ${cuadro.permite_grafica ? `
                                        <button type="button" class="btn btn-outline-primary me-2" id="btn-toggle-grafica-${modalId}">
                                            <i class="bi bi-graph-up-arrow"></i> Vista gráfica
                                        </button>
                                    ` : ''}
                                    <button type="button" class="btn btn-outline-success me-2 d-none" id="btn-toggle-excel-${modalId}">
                                        <i class="bi bi-table"></i> Vista Dataset
                                    </button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                `;
            }

            document.body.insertAdjacentHTML('beforeend', modalHTML);

            document.getElementById(modalId).addEventListener('hidden.bs.modal', function() {
                document.getElementById(modalId).remove();
                isModalOpen = false;
            });

            const modal = new bootstrap.Modal(document.getElementById(modalId));
            modal.show();

            // Si no es Mapa PDF, mantener la lógica de gráficos/excel existente
            if (!isMapaPdf) {
                const btnGrafica = document.getElementById(`btn-toggle-grafica-${modalId}`);
                const btnExcel = document.getElementById(`btn-toggle-excel-${modalId}`);
                const excelView = document.getElementById(`excel-view-${modalId}`);
                const graficaView = document.getElementById(`grafica-view-${modalId}`);

                if (btnGrafica) {
                    btnGrafica.addEventListener('click', function() {
                        excelView.style.display = 'none';
                        graficaView.style.display = '';
                        btnGrafica.classList.add('d-none');
                        btnExcel.classList.remove('d-none');
                        if (!graficaView.dataset.loaded) {
                            if (window.GraficaModalEngine && typeof window.GraficaModalEngine.renderGraficaInContainer === 'function') {
                                window.GraficaModalEngine.renderGraficaInContainer(
                                    `grafica-container-${modalId}`,
                                    excelUrl,
                                    data.excel.nombre_archivo
                                );
                                graficaView.dataset.loaded = "1";
                            } else {
                                document.getElementById(`grafica-container-${modalId}`).innerHTML = `
                                    <div class="alert alert-danger">No se encontró el motor de gráficas.</div>
                                `;
                            }
                        }
                    });
                }
                if (btnExcel) {
                    btnExcel.addEventListener('click', function() {
                        graficaView.style.display = 'none';
                        excelView.style.display = '';
                        btnExcel.classList.add('d-none');
                        btnGrafica.classList.remove('d-none');
                    });
                }

                if (tieneExcel) {
                    cargarExcelEnModal(modalId, excelUrl, data.excel.nombre_archivo, pdfUrl ? pdfUrl : null, formatedExcelUrl);
                } else {
                    document.getElementById(`excel-container-${modalId}`).innerHTML = `
                        <div class="alert alert-warning text-center">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            ${data.excel.tiene_archivo && !data.excel.archivo_existe ? 
                              'El archivo Excel asociado no se encuentra en el servidor.' : 
                              'Este cuadro no tiene un archivo Excel asociado.'}
                        </div>
                    `;
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert(`Error: ${error.message}`);
        });
}

function cargarExcelEnModal(modalId, excelUrl, fileName, pdfUrl = null, excelFormatedUrl = null) {
    const excelContainer = document.getElementById(`excel-container-${modalId}`);
    
    window.ExcelModalEngine.renderExcelInContainer(`excel-container-${modalId}`, excelUrl, fileName, pdfUrl, excelFormatedUrl)
        .catch(error => {
            console.error('Error al cargar Excel con el motor:', error);
        });
}
</script>