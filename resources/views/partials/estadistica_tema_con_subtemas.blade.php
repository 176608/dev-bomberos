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
                                        <!-- Columna de la imagen (6 columnas) -->
                                        <div class="col-6 subtema-image-container">
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
                                        
                                        <!-- Columna del texto (6 columnas) -->
                                        <div class="col-6 subtema-texto">
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
                        <a href="{{ url('/sigem?section=estadistica') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Volver a temas
                        </a>
                    </div>

                    <!-- Encabezado de subtema seleccionado -->
                    <div class="p-3 border-bottom" id="subtema-header">
                        @if(isset($subtema_seleccionado))
                            <h5 class="mb-0">{{ $subtema_seleccionado->subtema_titulo }}</h5>
                            <p class="text-muted small mb-0">{{ $tema_seleccionado->tema_titulo }}</p>
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
                                            <div class="col-12">
                                                <span class="fw-bold d-block text-success">
                                                    <i class="bi bi-file-earmark-excel me-2"></i>
                                                    {{ $cuadro['codigo_cuadro'] ?? 'N/A' }}
                                                </span>
                                                <span class="mb-1 d-block text-dark">{{ $cuadro['cuadro_estadistico_titulo'] ?? 'Sin título' }}</span>
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
        //console.log('SheetJS cargado preventivamente');
    }
});

// Función para inicializar el toggle del sidebar
function initSidebarToggle() {
    const sidebar = document.getElementById('sidebar-subtemas');
    const content = document.getElementById('contenido-principal');
    const toggleBtn = document.getElementById('toggle-sidebar');
    
    // Verificar si hay preferencia guardada
    const sidebarCollapsed = localStorage.getItem('subtemas-sidebar-collapsed') === 'true';
    
    // Aplicar estado inicial según preferencia
    if (sidebarCollapsed) {
        collapseSidebar();
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
    console.log('Cargando subtema ID:', subtema_id);
    
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
    const temaSeleccionado = document.querySelector('#tema-selector option:checked')?.text || 'Tema';
    
    // Actualizar de forma preliminar mientras se carga la información completa
    const headerContainer = document.getElementById('subtema-header');
    if (headerContainer) {
        headerContainer.innerHTML = `
            <h5 class="mb-0">${subtemaSeleccionado}</h5>
            <p class="text-muted small mb-0">${temaSeleccionado}</p>
        `;
    }
    
    // 2. LUEGO cargar los cuadros del subtema mediante AJAX
    fetch('{{ url("/sigem/obtener-cuadros-estadistica") }}/' + subtema_id)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Actualizar encabezado con información completa del subtema
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

// Función para actualizar el encabezado del subtema
function actualizarEncabezadoSubtema(subtema_id) {
    // Obtener información del subtema seleccionado
    fetch('{{ url("/sigem/obtener-info-subtema") }}/' + subtema_id)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const headerContainer = document.getElementById('subtema-header');
                headerContainer.innerHTML = `
                    <h5 class="mb-0">${data.subtema.subtema_titulo}</h5>
                    <p class="text-muted small mb-0">${data.subtema.tema ? data.subtema.tema.tema_titulo : ''}</p>
                `;
            }
        })
        .catch(error => console.error('Error al obtener info del subtema:', error));
}

// Función para renderizar cuadros
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
    
    // Función para extraer el número del índice después del último punto
    function extraerNumeroIndice(codigoCuadro) {
        // Verificar si el código está vacío
        if (!codigoCuadro) {
            return Number.MAX_VALUE; // Valor alto para que queden al final
        }
        
        // Patrón para extraer el número del índice (último componente después del punto)
        const match = codigoCuadro.match(/\.(\d+(?:\.\d+)*)$/);
        if (match) {
            return parseFloat(match[1]); // Convertir a número para ordenamiento correcto
        } else {
            const matchEnd = codigoCuadro.match(/(\d+(?:\.\d+)*)$/);
            if (matchEnd) {
                // Si no hay punto pero termina en número
                return parseFloat(matchEnd[1]);
            }
        }
        
        return Number.MAX_VALUE; // Por defecto al final
    }
    
    // Ordenar los cuadros por el número de índice
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
                    <div class="col-12">
                        <span class="fw-bold d-block text-success">
                            <i class="bi bi-file-earmark-excel me-2"></i>
                            ${cuadro.codigo_cuadro || 'N/A'}
                        </span>
                        <span class="mb-1 d-block text-dark">${cuadro.cuadro_estadistico_titulo || 'Sin título'}</span>
                        ${cuadro.cuadro_estadistico_subtitulo ? `<small class="text-muted d-block">${cuadro.cuadro_estadistico_subtitulo}</small>` : ''}

                    </div>
                </div>
            </a>
        `;
    });
    
    html += '</div>';
    container.innerHTML = html;
}

// Escuchar el evento personalizado desde sigem.js
document.addEventListener('verCuadroEstadistico', function(event) {
    const { cuadroId, codigo } = event.detail;
    console.log(`Blade: Recibiendo evento para mostrar cuadro: ID=${cuadroId}, Código=${codigo}`);
    mostrarModalCuadroSimple(cuadroId, codigo);
});


/*
Imprime el MODAL
*/


// Función simplificada para mostrar el modal con Excel
function mostrarModalCuadroSimple(cuadroId, codigo) {
    console.log(`Mostrando modal para cuadro ID=${cuadroId}, Código=${codigo}`);
    
    // Obtener información del cuadro
    fetch(`/sigem/obtener-excel-cuadro/${cuadroId}`)
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
            console.log('Información del cuadro:', cuadro);
            
            // Verificar si tiene archivo Excel y si existe físicamente
            const tieneExcel = data.tiene_excel && data.archivo_existe;
            const excelUrl = data.excel_url;
            
            // Crear modal básico
            const modalId = `modal_excel_${Date.now()}`;
            const modalHTML = `
                <div class="modal fade" id="${modalId}" tabindex="-1" aria-labelledby="excelModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header bg-success text-white">
                                <h5 class="modal-title">
                                    <i class="bi bi-file-earmark-excel me-2"></i>
                                    ${cuadro.codigo_cuadro} - ${cuadro.cuadro_estadistico_titulo}
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                            </div>
                            <div class="modal-body p-0">
                                <div id="excel-container-${modalId}" class="p-3">
                                    <div class="text-center py-5">
                                        <div class="spinner-border text-success" role="status"></div>
                                        <p class="mt-3">Cargando archivo Excel...</p>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                ${tieneExcel ? `
                                    <a href="${excelUrl}" class="btn btn-success" download>
                                        <i class="bi bi-download me-1"></i>Descargar Excel
                                    </a>
                                ` : ''}
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Agregar modal al DOM
            document.body.insertAdjacentHTML('beforeend', modalHTML);
            
            // Remover del DOM al cerrar
            document.getElementById(modalId).addEventListener('hidden.bs.modal', function() {
                document.getElementById(modalId).remove();
            });
            
            // Mostrar el modal
            const modal = new bootstrap.Modal(document.getElementById(modalId));
            modal.show();
            
            // Cargar Excel en el modal si existe
            if (tieneExcel) {
                cargarExcelEnModal(modalId, excelUrl, data.nombre_archivo);
            } else {
                // Mostrar mensaje si no hay Excel
                document.getElementById(`excel-container-${modalId}`).innerHTML = `
                    <div class="alert alert-warning text-center">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        ${data.tiene_excel && !data.archivo_existe ? 
                          'El archivo Excel asociado no se encuentra en el servidor.' : 
                          'Este cuadro no tiene un archivo Excel asociado.'}
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert(`Error: ${error.message}`);
        });
}

// Función para cargar y mostrar Excel
function cargarExcelEnModal(modalId, excelUrl, fileName) {
    console.log(`Cargando Excel desde: ${excelUrl}`);
    const excelContainer = document.getElementById(`excel-container-${modalId}`);
    
    // Cargar SheetJS si no está disponible
    if (typeof XLSX === 'undefined') {
        excelContainer.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-success" role="status"></div>
                <p class="mt-3">Cargando biblioteca para visualizar Excel...</p>
            </div>
        `;
        
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js';
        script.onload = () => {
            console.log('SheetJS cargado correctamente');
            cargarArchivo();
        };
        script.onerror = () => {
            console.error('Error al cargar SheetJS');
            mostrarError('No se pudo cargar la biblioteca para visualizar Excel');
        };
        document.head.appendChild(script);
    } else {
        cargarArchivo();
    }
    
    function cargarArchivo() {
        excelContainer.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-success" role="status"></div>
                <p class="mt-3">Procesando archivo Excel...</p>
            </div>
        `;
        
        // Obtener el archivo Excel mediante fetch
        fetch(excelUrl)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Error al cargar el archivo Excel (${response.status})`);
                }
                return response.arrayBuffer();
            })
            .then(arrayBuffer => {
                try {
                    // Procesar el archivo Excel con SheetJS
                    const data = new Uint8Array(arrayBuffer);
                    const workbook = XLSX.read(data, { type: 'array' });
                    
                    // Obtener la primera hoja
                    const firstSheetName = workbook.SheetNames[0];
                    const worksheet = workbook.Sheets[firstSheetName];
                    
                    // Obtener celdas combinadas
                    const mergedCells = worksheet['!merges'] || [];
                    
                    // Crear tabla HTML usando la utilidad de SheetJS
                    const htmlOptions = { 
                        sheet: firstSheetName,
                        header: true
                    };
                    
                    // Obtener el HTML y envolver en contenedor
                    const htmlString = XLSX.utils.sheet_to_html(worksheet, htmlOptions);
                    
                    // Mostrar en el contenedor
                    excelContainer.innerHTML = `
                        <div class="excel-viewer-container">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">
                                    <i class="bi bi-file-earmark-excel me-2"></i>
                                    ${fileName}
                                </h5>
                                <a href="${excelUrl}" class="btn btn-sm btn-outline-success" download>
                                    <i class="bi bi-download me-1"></i>Descargar
                                </a>
                            </div>
                            <div class="table-responsive excel-table-wrapper">
                                ${htmlString}
                            </div>
                        </div>
                    `;
                    
                    // Aplicar estilos a la tabla generada
                    const tablaExcel = excelContainer.querySelector('table');
                    if (tablaExcel) {
                        // Aplicar clases de Bootstrap
                        tablaExcel.className = 'table table-bordered excel-table';
                        
                        // Procesar las celdas combinadas
                        aplicarCeldasCombinadas(tablaExcel, mergedCells, worksheet);
                    }
                } catch (error) {
                    console.error('Error al procesar Excel:', error);
                    mostrarError(`Error al procesar el Excel: ${error.message}`);
                }
            })
            .catch(error => {
                console.error('Error en fetch:', error);
                mostrarError(`Error al cargar el archivo: ${error.message}`);
            });
    }
    
    function mostrarError(mensaje) {
        console.error(mensaje);
        excelContainer.innerHTML = `
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle me-2"></i>
                ${mensaje}
            </div>
            <div class="text-center mt-3">
                <a href="${excelUrl}" class="btn btn-primary" download>
                    <i class="bi bi-download me-2"></i>Intentar descargar directamente
                </a>
            </div>
        `;
    }
}

// Función auxiliar para aplicar celdas combinadas
function aplicarCeldasCombinadas(tabla, mergedCells, worksheet) {
    // Aplicar rowspan y colspan para celdas combinadas
    mergedCells.forEach(range => {
        try {
            const { s: start, e: end } = range;
            const rowspan = end.r - start.r + 1;
            const colspan = end.c - start.c + 1;
            
            // Si hay más de una fila o columna combinada
            if (rowspan > 1 || colspan > 1) {
                // La tabla de SheetJS tiene una fila de encabezado
                const fila = start.r + 1;
                const columna = start.c;
                
                // Verificar si existe la fila
                if (tabla.rows[fila]) {
                    const celda = tabla.rows[fila].cells[columna];
                    if (celda) {
                        // Aplicar atributos de combinación
                        if (rowspan > 1) celda.rowSpan = rowspan;
                        if (colspan > 1) celda.colSpan = colspan;
                    }
                }
            }
        } catch (e) {
            console.warn('Error al aplicar celda combinada:', e);
        }
    });
}
</script>