// Funciones globales de focus
function focusEnTema(numeroTema) {
    console.log(`Focus en tema: ${numeroTema}`);
    
    const temaElement = document.getElementById(`tema-cuadros-${numeroTema}`);
    const cuadrosContainer = document.getElementById('cuadros-container');
    
    if (temaElement && cuadrosContainer) {
        document.querySelectorAll('.highlight-focus').forEach(el => {
            el.classList.remove('highlight-focus');
        });
        
        cuadrosContainer.scrollTo({
            top: temaElement.offsetTop - cuadrosContainer.offsetTop,
            behavior: 'smooth'
        });
        
        temaElement.classList.add('highlight-focus');
        
        setTimeout(() => {
            temaElement.classList.remove('highlight-focus');
        }, 3000);
    }
}

function focusEnSubtema(numeroTema, ordenSubtema) {
    console.log(`Focus en subtema: Tema ${numeroTema}, Subtema ${ordenSubtema}`);
    
    const subtemaElement = document.getElementById(`subtema-cuadros-${numeroTema}-${ordenSubtema}`);
    const cuadrosContainer = document.getElementById('cuadros-container');
    
    if (subtemaElement && cuadrosContainer) {
        document.querySelectorAll('.highlight-focus').forEach(el => {
            el.classList.remove('highlight-focus');
        });
        
        cuadrosContainer.scrollTo({
            top: subtemaElement.offsetTop - cuadrosContainer.offsetTop,
            behavior: 'smooth'
        });
        
        subtemaElement.classList.add('highlight-focus');
        
        setTimeout(() => {
            subtemaElement.classList.remove('highlight-focus');
        }, 3000);
    }
}

function verCuadro(cuadroId, codigo) {
    console.log(`Abriendo cuadro: ID=${cuadroId}, Código=${codigo}`);
    
    const baseUrl = window.SIGEM_BASE_URL || 
                   (window.location.pathname.includes('/m_aux/') ? '/m_aux/public/sigem' : '/sigem');
    const url = `${baseUrl}/estadistica/${cuadroId}`;
    
    window.open(url, '_blank');
}

document.addEventListener('DOMContentLoaded', function() {
    // Elementos del DOM
    const navLinks = document.querySelectorAll('.sigem-nav-link');
    const contentContainer = document.getElementById('sigem-content');

    // PERSISTENCIA: Clave para localStorage
    const STORAGE_KEY = 'sigem_current_section';

    // FUNCIÓN: Obtener sección desde URL o localStorage
    function getCurrentSection() {
        // 1. Verificar parámetro URL
        const urlParams = new URLSearchParams(window.location.search);
        const urlSection = urlParams.get('section');
        
        if (urlSection && ['catalogo', 'estadistica', 'cartografia', 'productos'].includes(urlSection)) {
            return urlSection;
        }
        
        // 2. Verificar localStorage
        const storedSection = localStorage.getItem(STORAGE_KEY);
        if (storedSection && ['catalogo', 'estadistica', 'cartografia', 'productos'].includes(storedSection)) {
            return storedSection;
        }
        
        // 3. Por defecto: catálogo
        return 'catalogo';
    }

    // FUNCIÓN: Guardar sección actual
    function saveCurrentSection(section) {
        localStorage.setItem(STORAGE_KEY, section);
        
        // Actualizar URL sin recargar página
        const newUrl = new URL(window.location);
        newUrl.searchParams.set('section', section);
        window.history.replaceState({}, '', newUrl);
    }

    // FUNCIÓN: Actualizar menú activo
    function updateActiveMenu(activeSection) {
        navLinks.forEach(link => {
            const section = link.getAttribute('data-section');
            if (section === activeSection) {
                link.classList.add('active');
            } else {
                link.classList.remove('active');
            }
        });
    }

    // FUNCIÓN: Cargar contenido (usando variables globales)
    function loadContent(section) {
        console.log(`Cargando sección: ${section}`);
        
        // Guardar sección actual
        saveCurrentSection(section);
        
        // Actualizar menú
        updateActiveMenu(section);
        
        // Mostrar loading
        contentContainer.innerHTML = `
            <div class="Cargando">
                <i class="bi bi-hourglass-split"></i>
                <p>Cargando ${section}...</p>
            </div>
        `;
        
        // USAR variable global o fallback
        const baseUrl = window.SIGEM_BASE_URL || 
                       (window.location.pathname.includes('/m_aux/') ? '/m_aux/public/sigem' : '/sigem');
        const fullUrl = `${baseUrl}/partial/${section}`;
        
        console.log(`Base URL: ${baseUrl}`);
        console.log(`URL completa: ${fullUrl}`);
        
        // Cargar partial
        fetch(fullUrl)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.text();
            })
            .then(html => {
                contentContainer.innerHTML = html;
                
                // Ejecutar funciones específicas por sección
                if (section === 'cartografia') {
                    loadMapasData();
                } else if (section === 'catalogo') {
                    loadCatalogoData();
                }
            })
            .catch(error => {
                console.error('Error al cargar contenido:', error);
                contentContainer.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle"></i>
                        Error al cargar contenido de <strong>${section}</strong>
                        <br><small>Error: ${error.message}</small>
                        <br><small>URL intentada: ${fullUrl}</small>
                    </div>
                `;
            });
    }

    // FUNCIÓN: Cargar datos de mapas
    function loadMapasData() {
        const baseUrl = window.SIGEM_BASE_URL || 
                       (window.location.pathname.includes('/m_aux/') ? '/m_aux/public/sigem' : '/sigem');
        
        fetch(`${baseUrl}/mapas`)
            .then(response => response.json())
            .then(data => {
                const mapasContainer = document.getElementById('mapas-container');
                if (mapasContainer && data.success) {
                    mapasContainer.innerHTML = generateMapasHtml(data);
                }
            })
            .catch(error => {
                console.error('Error cargando mapas:', error);
                const mapasContainer = document.getElementById('mapas-container');
                if (mapasContainer) {
                    mapasContainer.innerHTML = `
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i>
                            Error al cargar mapas: ${error.message}
                        </div>
                    `;
                }
            });
    }

    // FUNCIÓN: Cargar datos de catálogo
    function loadCatalogoData() {
        const baseUrl = window.SIGEM_BASE_URL || 
                       (window.location.pathname.includes('/m_aux/') ? '/m_aux/public/sigem' : '/sigem');
        
        fetch(`${baseUrl}/catalogo`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const indiceContainer = document.getElementById('indice-container');
                    const cuadrosContainer = document.getElementById('cuadros-container');
                    
                    if (indiceContainer && data.temas_detalle) {
                        indiceContainer.innerHTML = generateEstructuraIndice(data.temas_detalle);
                    }
                    
                    if (cuadrosContainer && data.cuadros_estadisticos) {
                        cuadrosContainer.innerHTML = generateListaCuadros(data.cuadros_estadisticos);
                    }
                    
                    sincronizarAlturas();
                }
            })
            .catch(error => {
                console.error('Error cargando catálogo:', error);
                const indiceContainer = document.getElementById('indice-container');
                const cuadrosContainer = document.getElementById('cuadros-container');
                
                if (indiceContainer) {
                    indiceContainer.innerHTML = `<div class="alert alert-warning">Error al cargar índice: ${error.message}</div>`;
                }
                if (cuadrosContainer) {
                    cuadrosContainer.innerHTML = `<div class="alert alert-warning">Error al cargar cuadros: ${error.message}</div>`;
                }
            });
    }

    // FUNCIÓN: Generar HTML para mapas (LAYOUT LIMPIO)
    function generateMapasHtml(data) {
        let html = `
            <div class="mb-3">
                <div class="alert alert-info d-flex align-items-center">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Mapas disponibles: ${data.total_mapas}</strong>
                </div>
            </div>
        `;
        
        if (data.mapas && data.mapas.length > 0) {
            data.mapas.forEach((mapa, index) => {
                html += `
                    <div class="row">
                        <div class="col-12">
                            <div class="mapa-row">
                                <!-- HEADER: Título (10 col) + Botón (2 col) -->
                                <div class="mapa-header">
                                    <div class="row align-items-center">
                                        <div class="col-10">
                                            <h4 class="mapa-title mb-1">
                                                <i class="bi bi-geo-alt-fill me-2"></i>
                                                ${mapa.nombre_mapa || 'Mapa sin nombre'}
                                            </h4>
                                            <p class="mapa-seccion mb-0">
                                                <i class="bi bi-folder-fill me-1"></i>
                                                Sección: ${mapa.nombre_seccion || 'No especificada'}
                                            </p>
                                        </div>
                                        <div class="col-2 text-end">
                                            ${mapa.enlace ? 
                                                `<a href="${mapa.enlace}" target="_blank" class="mapa-btn">
                                                    <i class="bi bi-box-arrow-up-right"></i>
                                                    Ver Mapa
                                                </a>` 
                                                : 
                                                `<span class="mapa-btn mapa-btn-disabled">
                                                    <i class="bi bi-x-circle"></i>
                                                    No disponible
                                                </span>`
                                            }
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- CONTENIDO: Imagen (izquierda) + Descripción (derecha) -->
                                <div class="mapa-content">
                                    <!-- IMAGEN (50% izquierda) - CLICKEABLE -->
                                    <div class="mapa-image-container" ${mapa.enlace ? `onclick="window.open('${mapa.enlace}', '_blank')" style="cursor: pointer;"` : ''}>
                                        ${mapa.tiene_imagen ? 
                                            `<img src="${mapa.imagen_url}" 
                                                  alt="${mapa.nombre_mapa}" 
                                                  class="mapa-image"
                                                  onload="console.log('✅ Imagen cargada: ${mapa.icono}')"
                                                  onerror="console.error('❌ Error cargando imagen: ${mapa.icono}'); this.style.display='none'; this.parentNode.classList.add('image-error');"
                                             >
                                             ${mapa.enlace ? 
                                                `<div class="mapa-image-overlay">
                                                    <i class="bi bi-zoom-in me-2"></i>
                                                    Ver Mapa Completo
                                                </div>` 
                                                : ''
                                             }
                                             <div class="image-error-placeholder" style="display: none;">
                                                <div class="mapa-image-placeholder">
                                                    <i class="bi bi-image"></i>
                                                    <h5>${mapa.nombre_mapa || 'Mapa'}</h5>
                                                    <p>Error al cargar imagen</p>
                                                    <small class="text-danger">Revisa la imagen: ${mapa.icono || 'N/A'}</small>
                                                </div>
                                             </div>` 
                                            : 
                                            getImagePlaceholder(mapa)
                                        }
                                    </div>
                                    
                                    <!-- DESCRIPCIÓN (50% derecha) -->
                                    <div class="mapa-descripcion">
                                        <h5>
                                            <i class="bi bi-card-text"></i>
                                            Descripción
                                        </h5>
                                        <p>
                                            ${mapa.descripcion || 'No hay descripción disponible para este mapa.'}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
        } else {
            html += `
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-warning text-center">
                            <i class="bi bi-exclamation-triangle fs-1"></i>
                            <h4 class="mt-3">No hay mapas disponibles</h4>
                            <p class="mb-0">Actualmente no hay mapas configurados en el sistema.</p>
                        </div>
                    </div>
                </div>
            `;
        }
        
        return html;
    }

    // FUNCIÓN AUXILIAR: Generar placeholder para imagen (LIMPIO)
    function getImagePlaceholder(mapa) {
        return `
            <div class="mapa-image-placeholder">
                <i class="bi bi-image"></i>
                <h5>${mapa.nombre_mapa || 'Mapa'}</h5>
                <p>
                    ${mapa.icono ? 
                        'Imagen no disponible' : 
                        'Sin imagen configurada'
                    }
                </p>
                ${mapa.enlace ? 
                    `<small class="text-primary">
                        <i class="bi bi-cursor-fill"></i>
                        Haz clic para ver mapa
                    </small>` 
                    : 
                    `<small class="text-muted">
                        Mapa no disponible
                    </small>`
                }
            </div>
        `;
    }

    // FUNCIÓN AUXILIAR: Placeholder escapado para onerror
    function getImagePlaceholderEscaped(mapa) {
        return `<div class="mapa-image-placeholder">
                    <i class="bi bi-image"></i>
                    <h5>${mapa.nombre_mapa || 'Mapa'}</h5>
                    <p>Error al cargar imagen</p>
                    <small class="text-danger">Archivo: ${mapa.icono || 'N/A'}</small>
                </div>`;
    }

    // FUNCIÓN: Generar estructura de índice (NUEVA UI MODERNA)
    function generateEstructuraIndice(temasDetalle) {
        let estructura = '';
        let totalSubtemas = 0;
        
        temasDetalle.forEach((tema, temaIndex) => {
            const numeroTema = temaIndex + 1;
            const temaClass = `tema-${numeroTema}`;
            
            // Contar subtemas
            totalSubtemas += tema.subtemas ? tema.subtemas.length : 0;
            
            estructura += `
                <div class="tema-item" data-tema="${numeroTema}">
                    <div class="tema-header ${temaClass}" 
                         onclick="toggleTema(${numeroTema})"
                         data-bs-toggle="tooltip" 
                         title="Click para expandir/contraer">
                        <span class="tema-numero">${numeroTema}.</span>
                        <span class="tema-texto">${tema.tema_titulo.toUpperCase()}</span>
                        <i class="bi bi-chevron-down tema-chevron" style="float: right;"></i>
                    </div>
                    
                    <div class="subtemas-list" id="subtemas-${numeroTema}" style="display: none;">
            `;

            if (tema.subtemas && tema.subtemas.length > 0) {
                tema.subtemas.forEach((subtema, subtemaIndex) => {
                    const ordenSubtema = subtema.orden_indice || subtemaIndex;
                    
                    estructura += `
                        <div class="subtema-item" 
                             onclick="focusEnSubtema(${numeroTema}, ${ordenSubtema})"
                             data-tema="${numeroTema}"
                             data-subtema="${ordenSubtema}"
                             title="Click para ir al subtema">
                            <div class="subtema-codigo">
                                ${subtema.clave_subtema || tema.clave_tema || 'N/A'}
                            </div>
                            <div class="subtema-titulo">
                                ${subtema.subtema_titulo}
                            </div>
                        </div>
                    `;
                });
            } else {
                estructura += `
                    <div class="subtema-item no-subtemas">
                        <div class="subtema-titulo text-muted">
                            <i class="bi bi-info-circle me-2"></i>
                            No hay subtemas disponibles
                        </div>
                    </div>
                `;
            }

            estructura += '</div></div>';
        });
        
        // Actualizar estadísticas
        setTimeout(() => {
            document.getElementById('stats-temas').textContent = temasDetalle.length;
            document.getElementById('stats-subtemas').textContent = totalSubtemas;
        }, 100);
        
        return estructura;
    }

    // FUNCIÓN: Toggle de tema (expandir/contraer)
    window.toggleTema = function(numeroTema) {
        const subtemasContainer = document.getElementById(`subtemas-${numeroTema}`);
        const chevron = document.querySelector(`[data-tema="${numeroTema}"] .tema-chevron`);
        const temaHeader = document.querySelector(`[data-tema="${numeroTema}"] .tema-header`);
        
        if (subtemasContainer && chevron) {
            const isHidden = subtemasContainer.style.display === 'none';
            
            if (isHidden) {
                // Expandir
                subtemasContainer.style.display = 'block';
                chevron.classList.remove('bi-chevron-down');
                chevron.classList.add('bi-chevron-up');
                temaHeader.classList.add('active');
                
                // Scroll suave hacia el tema
                subtemasContainer.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'nearest' 
                });
            } else {
                // Contraer
                subtemasContainer.style.display = 'none';
                chevron.classList.remove('bi-chevron-up');
                chevron.classList.add('bi-chevron-down');
                temaHeader.classList.remove('active');
            }
        }
    };

    // FUNCIÓN: Expandir todos los temas
    window.expandirTodo = function() {
        const allSubtemas = document.querySelectorAll('[id^="subtemas-"]');
        const allChevrons = document.querySelectorAll('.tema-chevron');
        const allHeaders = document.querySelectorAll('.tema-header');
        
        allSubtemas.forEach(container => {
            container.style.display = 'block';
        });
        
        allChevrons.forEach(chevron => {
            chevron.classList.remove('bi-chevron-down');
            chevron.classList.add('bi-chevron-up');
        });
        
        allHeaders.forEach(header => {
            header.classList.add('active');
        });
    };

    // FUNCIÓN: Contraer todos los temas
    window.contraerTodo = function() {
        const allSubtemas = document.querySelectorAll('[id^="subtemas-"]');
        const allChevrons = document.querySelectorAll('.tema-chevron');
        const allHeaders = document.querySelectorAll('.tema-header');
        
        allSubtemas.forEach(container => {
            container.style.display = 'none';
        });
        
        allChevrons.forEach(chevron => {
            chevron.classList.remove('bi-chevron-up');
            chevron.classList.add('bi-chevron-down');
        });
        
        allHeaders.forEach(header => {
            header.classList.remove('active');
        });
    };

    // FUNCIÓN MEJORADA: Focus en subtema con highlight mejorado
    window.focusEnSubtema = function(numeroTema, ordenSubtema) {
        console.log(`Focus en subtema: Tema ${numeroTema}, Subtema ${ordenSubtema}`);
        
        // Limpiar highlights anteriores
        document.querySelectorAll('.highlight-focus').forEach(el => {
            el.classList.remove('highlight-focus');
        });
        
        document.querySelectorAll('.subtema-item.active').forEach(el => {
            el.classList.remove('active');
        });
        
        // Marcar subtema activo en sidebar
        const subtemaItemSidebar = document.querySelector(`[data-tema="${numeroTema}"][data-subtema="${ordenSubtema}"]`);
        if (subtemaItemSidebar) {
            subtemaItemSidebar.classList.add('active');
        }
        
        // Buscar y hacer focus en el contenido
        const subtemaElement = document.getElementById(`subtema-cuadros-${numeroTema}-${ordenSubtema}`);
        const cuadrosContainer = document.getElementById('cuadros-container');
        
        if (subtemaElement && cuadrosContainer) {
            cuadrosContainer.scrollTo({
                top: subtemaElement.offsetTop - cuadrosContainer.offsetTop - 20,
                behavior: 'smooth'
            });
            
            subtemaElement.classList.add('highlight-focus');
            
            setTimeout(() => {
                subtemaElement.classList.remove('highlight-focus');
            }, 3000);
        }
    };

    // Event listeners para botones de control
    document.addEventListener('DOMContentLoaded', function() {
        // Botones de control
        document.addEventListener('click', function(e) {
            if (e.target.id === 'btn-expandir-todo' || e.target.closest('#btn-expandir-todo')) {
                expandirTodo();
            }
            
            if (e.target.id === 'btn-contraer-todo' || e.target.closest('#btn-contraer-todo')) {
                contraerTodo();
            }
        });
    });

    // Event listeners para navegación
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const section = this.getAttribute('data-section');
            loadContent(section);
        });
    });
    
    // CARGAR CONTENIDO INICIAL (con persistencia)
    const initialSection = getCurrentSection();
    console.log(`Sección inicial: ${initialSection}`);
    loadContent(initialSection);
});