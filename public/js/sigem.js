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
    const url = `/sigem/estadistica/${cuadroId}`;
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

    // FUNCIÓN: Cargar contenido
    function loadContent(section) {
        console.log(`Cargando sección: ${section}`);
        console.log(`URL que se va a llamar: /sigem/partial/${section}`);
        console.log(`URL completa: ${window.location.origin}/sigem/partial/${section}`);
        
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
        
        // Cargar partial
        fetch(`/sigem/partial/${section}`)
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
                    </div>
                `;
            });
    }

    // FUNCIÓN: Cargar datos de mapas
    function loadMapasData() {
        fetch('/sigem/mapas')
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
                            Error al cargar mapas
                        </div>
                    `;
                }
            });
    }

    // FUNCIÓN: Cargar datos de catálogo
    function loadCatalogoData() {
        fetch('/sigem/catalogo')
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
                    indiceContainer.innerHTML = `<div class="alert alert-warning">Error al cargar índice</div>`;
                }
                if (cuadrosContainer) {
                    cuadrosContainer.innerHTML = `<div class="alert alert-warning">Error al cargar cuadros</div>`;
                }
            });
    }

    // FUNCIÓN: Generar HTML para mapas
    function generateMapasHtml(data) {
        let html = `<h4>Mapas disponibles (${data.total_mapas})</h4>`;
        
        if (data.mapas && data.mapas.length > 0) {
            html += '<div class="row">';
            data.mapas.forEach((mapa, index) => {
                html += `
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <strong>${mapa.nombre_mapa || 'Mapa sin nombre'}</strong>
                            </div>
                            <div class="card-body">
                                <p><strong>Sección:</strong> ${mapa.nombre_seccion || 'N/A'}</p>
                                <p><strong>Descripción:</strong> ${mapa.descripcion || 'N/A'}</p>
                                ${mapa.enlace ? `<a href="${mapa.enlace}" target="_blank" class="btn btn-primary btn-sm">Ver Mapa</a>` : ''}
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
        } else {
            html += '<div class="alert alert-info">No hay mapas disponibles</div>';
        }
        
        return html;
    }

    // FUNCIÓN: Generar estructura de índice
    function generateEstructuraIndice(temasDetalle) {
        let estructura = '<div style="font-size: 12px; overflow-y: auto;">';
        
        temasDetalle.forEach((tema, temaIndex) => {
            const colores = [
                'background-color: #8FBC8F;',
                'background-color: #87CEEB;',
                'background-color: #DDA0DD;',
                'background-color: #F0E68C;',
                'background-color: #FFA07A;',
                'background-color: #98FB98;'
            ];
            
            const colorTema = colores[temaIndex % colores.length];
            const numeroTema = temaIndex + 1;

            estructura += `
                <div class="mb-3" style="border: 1px solid #ddd;">
                    <div class="text-center text-white fw-bold py-2" 
                         style="${colorTema} cursor: pointer;" 
                         onclick="focusEnTema(${numeroTema});">
                        ${numeroTema}. ${tema.tema_titulo.toUpperCase()}
                    </div>
                    <div style="background-color: white;">
            `;

            if (tema.subtemas && tema.subtemas.length > 0) {
                tema.subtemas.forEach((subtema, subtemaIndex) => {
                    const bgColor = subtemaIndex % 2 === 0 ? 'background-color: #f8f9fa;' : 'background-color: white;';
                    const ordenSubtema = subtema.orden_indice || subtemaIndex;
                    
                    estructura += `
                        <div class="d-flex border-bottom" 
                             style="${bgColor} cursor: pointer;"
                             onclick="focusEnSubtema(${numeroTema}, ${ordenSubtema});">
                            <div class="px-3 py-2 text-center fw-bold" style="min-width: 60px; border-right: 1px solid #ddd;">
                                ${subtema.clave_subtema || tema.clave_tema || 'N/A'}
                            </div>
                            <div class="px-3 py-2 flex-grow-1">
                                ${subtema.subtema_titulo}
                            </div>
                        </div>
                    `;
                });
            }

            estructura += '</div></div>';
        });

        estructura += '</div>';
        return estructura;
    }

    // FUNCIÓN: Generar lista de cuadros (simplificada por ahora)
    function generateListaCuadros(cuadrosEstadisticos) {
        if (!cuadrosEstadisticos || cuadrosEstadisticos.length === 0) {
            return '<div class="alert alert-warning">No hay cuadros estadísticos disponibles</div>';
        }

        let html = '<div style="overflow-y: auto;">';
        
        // Por ahora, mostrar lista simple
        html += `<div class="alert alert-info">
            <i class="bi bi-info-circle"></i>
            Total de cuadros estadísticos: <strong>${cuadrosEstadisticos.length}</strong>
        </div>`;
        
        // Lista básica de cuadros
        html += '<div class="list-group">';
        cuadrosEstadisticos.slice(0, 10).forEach(cuadro => { // Mostrar solo primeros 10
            html += `
                <div class="list-group-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">${cuadro.cuadro_estadistico_titulo || 'Sin título'}</h6>
                            <small class="text-muted">Código: ${cuadro.codigo_cuadro || 'N/A'}</small>
                        </div>
                        <button class="btn btn-primary btn-sm" onclick="verCuadro(${cuadro.cuadro_estadistico_id}, '${cuadro.codigo_cuadro}')">
                            Ver
                        </button>
                    </div>
                </div>
            `;
        });
        html += '</div>';
        
        if (cuadrosEstadisticos.length > 10) {
            html += `<div class="text-center mt-3">
                <small class="text-muted">Mostrando 10 de ${cuadrosEstadisticos.length} cuadros</small>
            </div>`;
        }
        
        html += '</div>';
        return html;
    }

    // FUNCIÓN: Sincronizar alturas
    function sincronizarAlturas() {
        setTimeout(() => {
            const indiceContainer = document.getElementById('indice-container');
            const cuadrosContainer = document.getElementById('cuadros-container');
            
            if (indiceContainer && cuadrosContainer) {
                const alturaIndice = indiceContainer.scrollHeight;
                cuadrosContainer.style.height = alturaIndice + 'px';
            }
        }, 100);
    }

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