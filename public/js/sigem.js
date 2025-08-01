(function (window, document) {
    'use strict';

    // === CONFIGURACIÓN CENTRALIZADA ===
    const CONFIG = {
        STORAGE_KEY: 'sigem_current_section',
        SECTIONS: ['inicio', 'catalogo', 'estadistica', 'cartografia', 'productos'],
        DEFAULT_SECTION: 'inicio',
        BASE_URL: null, // Se determinará dinámicamente
        API_URL: null,  // Se determinará dinámicamente
        PARTIALS_URL: null // Se determinará dinámicamente
    };

    // === OBJETO PRINCIPAL DE LA APLICACIÓN ===
    const SIGEMApp = {

        // Elementos del DOM cacheados
        elements: {
            navLinks: null,
            contentContainer: null,
        },

        // Inicialización de la aplicación
        init: function () {
            console.log('Inicializando SIGEMApp ...');
            this.determineUrls();
            this.cacheElements();
            this.bindEvents();
            this.loadInitialContent();
        },

        // Determinar las URLs base dinámicamente
        determineUrls: function () {
            CONFIG.BASE_URL = window.SIGEM_BASE_URL ||
                (window.location.pathname.includes('/m_aux/') ? '/m_aux/public/sigem' : '/sigem');
            CONFIG.PARTIALS_URL = `${CONFIG.BASE_URL}/partial`;
            CONFIG.API_URL = CONFIG.BASE_URL; // Asumiendo que las rutas API están bajo la base
            console.log('URLs configuradas:', { base: CONFIG.BASE_URL, partials: CONFIG.PARTIALS_URL, api: CONFIG.API_URL });
        },

        // Cachear elementos del DOM
        cacheElements: function () {
            this.elements.navLinks = document.querySelectorAll('.sigem-nav-link');
            this.elements.contentContainer = document.getElementById('sigem-content');
        },

        // Enlazar eventos
        bindEvents: function () {
            this.elements.navLinks.forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const section = link.getAttribute('data-section');
                    this.loadContent(section);
                });
            });
        },

        // === GESTIÓN DE NAVEGACIÓN Y CONTENIDO ===

        getCurrentSection: function () {
            const urlParams = new URLSearchParams(window.location.search);
            const urlSection = urlParams.get('section');
            if (urlSection && CONFIG.SECTIONS.includes(urlSection)) {
                return urlSection;
            }
            const storedSection = localStorage.getItem(CONFIG.STORAGE_KEY);
            if (storedSection && CONFIG.SECTIONS.includes(storedSection)) {
                return storedSection;
            }
            return CONFIG.DEFAULT_SECTION;
        },

        saveCurrentSection: function (section) {
            localStorage.setItem(CONFIG.STORAGE_KEY, section);
            const newUrl = new URL(window.location);
            newUrl.searchParams.set('section', section);
            window.history.replaceState({}, '', newUrl);
        },

        updateActiveMenu: function (activeSection) {
            this.elements.navLinks.forEach(link => {
                const section = link.getAttribute('data-section');
                link.classList.toggle('active', section === activeSection);
            });
        },

        showLoading: function (section) {
            if (this.elements.contentContainer) {
                this.elements.contentContainer.innerHTML = `
                    <div class="Cargando">
                        <i class="bi bi-hourglass-split"></i>
                        <p>Cargando ${section}...</p>
                    </div>
                `;
            }
        },

        loadContent: function (section) {
            console.log(`Cargando sección: ${section}`);
            this.saveCurrentSection(section);
            this.updateActiveMenu(section);
            this.showLoading(section);

            const url = `${CONFIG.PARTIALS_URL}/${section}`;
            console.log(`URL completa: ${url}`); // Log informativo

            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.text();
                })
                .then(html => {
                    if (this.elements.contentContainer) {
                        this.elements.contentContainer.innerHTML = html;
                        this.executePostLoad(section);
                    }
                })
                .catch(error => {
                    console.error('Error al cargar contenido:', error);
                    if (this.elements.contentContainer) {
                        this.elements.contentContainer.innerHTML = `
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle"></i>
                                Error al cargar contenido de <strong>${section}</strong>
                                <br><small>Error: ${error.message}</small>
                                <br><small>URL intentada: ${url}</small>
                            </div>
                        `;
                    }
                });
        },

        executePostLoad: function (section) {
            switch (section) {
                case 'inicio':
                    this.loadInicioData();
                    break;
                case 'cartografia': // Carga datos para mapa.blade
                    this.loadMapasData();
                    break;
                case 'catalogo': // Carga datos para catalogo.blade
                    this.loadCatalogoData();
                    break;
                case 'estadistica': // Carga datos para estadistica.blade (puede incluir un cuadro específico)
                    this.loadEstadisticaData();
                    break;
            }
        },

        loadInitialContent: function () {
            const initialSection = this.getCurrentSection();
            this.loadContent(initialSection);
        },

        // === FUNCIONES GLOBALES EXPUESTAS ===
        // (Necesarias para ser llamadas desde HTML generado dinámicamente)

        // === FUNCION usada en la vista de catálogo.blade ===
        focusEnTema: function (numeroTema) {
            console.log(`Focus en tema: ${numeroTema}`);
            const temaElement = document.getElementById(`tema-cuadros-${numeroTema}`);
            const cuadrosContainer = document.getElementById('cuadros-container');
            if (temaElement && cuadrosContainer) {
                document.querySelectorAll('.highlight-focus').forEach(el => el.classList.remove('highlight-focus'));
                cuadrosContainer.scrollTo({
                    top: temaElement.offsetTop - cuadrosContainer.offsetTop,
                    behavior: 'smooth'
                });
                temaElement.classList.add('highlight-focus');
                setTimeout(() => temaElement.classList.remove('highlight-focus'), 3000);
            } else {
                console.warn(`No se encontró elemento para tema ${numeroTema}.`);
            }
        },

        // === FUNCION usada en la vista de catálogo.blade ===
        focusEnSubtema: function (numeroTema, ordenSubtema) {
            console.log(`Focus en subtema: Tema ${numeroTema}, Subtema ${ordenSubtema}`);
            const subtemaElement = document.getElementById(`subtema-cuadros-${numeroTema}-${ordenSubtema}`);
            const cuadrosContainer = document.getElementById('cuadros-container');
            if (subtemaElement && cuadrosContainer) {
                document.querySelectorAll('.highlight-focus').forEach(el => el.classList.remove('highlight-focus'));
                cuadrosContainer.scrollTo({
                    top: subtemaElement.offsetTop - cuadrosContainer.offsetTop,
                    behavior: 'smooth'
                });
                subtemaElement.classList.add('highlight-focus');
                setTimeout(() => subtemaElement.classList.remove('highlight-focus'), 3000);
            } else {
                console.warn(`No se encontró elemento para subtema ${numeroTema}-${ordenSubtema}.`);
            }
        },

        // === FUNCION usada en la vista de catálogo.blade ===
        verCuadro: function (cuadroId, codigo) {
            console.log(`Abriendo cuadro: ID=${cuadroId}, Código=${codigo}`);
            const url = `${CONFIG.BASE_URL}?section=estadistica&cuadro_id=${cuadroId}`;
            console.log(`Usando URL: ${url}`);
            window.open(url, '_blank');
        }
    };

    // === FUNCIONES DE UTILIDAD ===

    // *** AL FINAL: EXPONER NUEVAS FUNCIONES AL SCOPE GLOBAL ***
    // Estas funciones se usan en el HTML generado por otras funciones JS

    // === FUNCION usada en la vista de catálogo.blade ===
    function sincronizarAlturas() {
        setTimeout(() => {
            const indiceContainer = document.getElementById('indice-container');
            const cuadrosContainer = document.getElementById('cuadros-container');
            if (indiceContainer && cuadrosContainer) {
                indiceContainer.style.height = 'auto';
                cuadrosContainer.style.height = 'auto';

                const alturaIndice = indiceContainer.scrollHeight;
                const alturaCuadros = cuadrosContainer.scrollHeight;
                const alturaMaxima = Math.max(alturaIndice, alturaCuadros);

                if (alturaMaxima > 0) {
                    indiceContainer.style.height = alturaMaxima + 'px';
                    cuadrosContainer.style.height = alturaMaxima + 'px';
                }
            }
        }, 100);
    }

    // === FUNCION usada en la vista de catálogo.blade ===
    function organizarCuadrosPorTema(cuadrosEstadisticos) {
        const organizacion = {};
        cuadrosEstadisticos.forEach(cuadro => {
            const subtemaInfo = cuadro.subtema || { subtema_id: 'sin_subtema', subtema_titulo: 'Sin Subtema', orden_indice: 0 };
            const temaInfo = subtemaInfo.tema || { tema_id: 'sin_tema', tema_titulo: 'Sin Tema Info', orden_indice: 0 };
            const temaKey = `tema_${temaInfo.tema_id}`;
            const subtemaKey = `subtema_${subtemaInfo.subtema_id}`;

            if (!organizacion[temaKey]) {
                organizacion[temaKey] = {
                    tema_info: temaInfo,
                    subtemas: {}
                };
            }
            if (!organizacion[temaKey].subtemas[subtemaKey]) {
                organizacion[temaKey].subtemas[subtemaKey] = {
                    subtema_info: subtemaInfo,
                    cuadros: []
                };
            }
            organizacion[temaKey].subtemas[subtemaKey].cuadros.push(cuadro);
        });

        const organizacionOrdenada = {};
        Object.keys(organizacion)
            .sort((a, b) => (organizacion[a].tema_info.orden_indice || 0) - (organizacion[b].tema_info.orden_indice || 0))
            .forEach(temaKey => {
                const tema = organizacion[temaKey];
                organizacionOrdenada[temaKey] = tema;

                const subtemasOrdenados = {};
                Object.keys(tema.subtemas)
                    .sort((a, b) => (tema.subtemas[a].subtema_info.orden_indice || 0) - (tema.subtemas[b].subtema_info.orden_indice || 0))
                    .forEach(subtemaKey => {
                        const subtema = tema.subtemas[subtemaKey];
                        subtema.cuadros.sort((a, b) =>
                            compararCodigosCuadro(
                                a.codigo_cuadro || '',
                                b.codigo_cuadro || '',
                                a.subtema || subtema.subtema_info,
                                b.subtema || subtema.subtema_info
                            )
                        );
                        subtemasOrdenados[subtemaKey] = subtema;
                    });
                organizacionOrdenada[temaKey].subtemas = subtemasOrdenados;
            });

        return organizacionOrdenada;
    }

    // === FUNCION usada en la vista de catálogo.blade ===
    function compararCodigosCuadro(codigoA, codigoB, subtemaInfoA, subtemaInfoB) {
        const ordenSubtemaA = subtemaInfoA?.orden_indice || 0;
        const ordenSubtemaB = subtemaInfoB?.orden_indice || 0;
        if (ordenSubtemaA !== ordenSubtemaB) {
            return ordenSubtemaA - ordenSubtemaB;
        }

        function extraerNumero(codigo) {
            if (!codigo) return 0;
            const partes = codigo.split('.');
            if (partes.length >= 3) {
                const numeroStr = partes[2];
                const numero = parseInt(numeroStr, 10);
                return isNaN(numero) ? 0 : numero;
            }
            return 0;
        }
        const numeroA = extraerNumero(codigoA);
        const numeroB = extraerNumero(codigoB);
        return numeroA - numeroB;
    }


    // === FUNCIONES DE CARGA DE DATOS POR SECCIÓN ===

    // *** FUNCIONES DE CARGA DE DATOS PARA VISTAS BLADE ESPECÍFICAS ***

    // === FUNCION usada en la vista de cartografía.blade ===
    SIGEMApp.loadMapasData = function () {
        const mapasContainer = document.getElementById('mapas-container');
        if (!mapasContainer) return;

        mapasContainer.innerHTML = '<div class="text-center py-3"><div class="spinner-border text-success" role="status"><span class="visually-hidden">Cargando...</span></div></div>';

        fetch(`${CONFIG.API_URL}/mapas`)
            .then(response => response.json())
            .then(data => {
                if (mapasContainer && data.success) {
                    mapasContainer.innerHTML = generateMapasHtml(data);
                }
            })
            .catch(error => {
                console.error('Error cargando mapas:', error);
                if (mapasContainer) {
                    mapasContainer.innerHTML = `<div class="alert alert-warning"><i class="bi bi-exclamation-triangle"></i>Error al cargar mapas: ${error.message}</div>`;
                }
            });
    };

    // === FUNCION usada en la vista de catálogo.blade ===
    SIGEMApp.loadCatalogoData = function () {
        const indiceContainer = document.getElementById('indice-container');
        const cuadrosContainer = document.getElementById('cuadros-container');

        if (indiceContainer) indiceContainer.innerHTML = '<div class="text-center py-3"><div class="spinner-border text-success" role="status"><span class="visually-hidden">Cargando índice...</span></div></div>';
        if (cuadrosContainer) cuadrosContainer.innerHTML = '<div class="text-center py-3"><div class="spinner-border text-success" role="status"><span class="visually-hidden">Cargando cuadros...</span></div></div>';

        fetch(`${CONFIG.API_URL}/catalogo`)
            .then(response => response.json())
            .then(data => {
                console.log('Response completa:', data); // Log solicitado
                if (data.success) {
                    if (indiceContainer && data.temas_detalle) {
                        indiceContainer.innerHTML = generateEstructuraIndice(data.temas_detalle);
                    }
                    if (cuadrosContainer && data.cuadros_estadisticos) {
                        cuadrosContainer.innerHTML = generateListaCuadros(data.cuadros_estadisticos);
                        sincronizarAlturas();
                    }
                } else {
                    const errorMsg = `<div class="alert alert-danger"><i class="bi bi-exclamation-circle"></i> Error al cargar catálogo: ${data.message}</div>`;
                    if (indiceContainer) indiceContainer.innerHTML = errorMsg;
                    if (cuadrosContainer) cuadrosContainer.innerHTML = errorMsg;
                }
            })
            .catch(error => {
                console.error('Error cargando catálogo:', error);
                const errorMsg = `<div class="alert alert-danger"><i class="bi bi-exclamation-circle"></i> Error de conexión al cargar catálogo</div>`;
                if (indiceContainer) indiceContainer.innerHTML = errorMsg;
                if (cuadrosContainer) cuadrosContainer.innerHTML = errorMsg;
            });
    };

    // === FUNCION usada en la vista de inicio.blade ===
    SIGEMApp.loadInicioData = function () {
        fetch(`${CONFIG.API_URL}/datos-inicio`)
            .then(response => response.json())
            .then(data => {
                console.log('Datos de inicio cargados:', data);
                if (data.success && data.estadisticas) {
                    const statTemas = document.getElementById('stat-temas');
                    const statSubtemas = document.getElementById('stat-subtemas');
                    const statCuadros = document.getElementById('stat-cuadros');
                    if (statTemas) statTemas.textContent = data.estadisticas.total_temas || 0;
                    if (statSubtemas) statSubtemas.textContent = data.estadisticas.total_subtemas || 0;
                    if (statCuadros) statCuadros.textContent = data.estadisticas.total_cuadros || 0;
                }
            })
            .catch(error => {
                console.error('Error cargando datos de inicio:', error);
            });
    };

    // === FUNCION usada en la vista de estadística.blade ===
    SIGEMApp.loadEstadisticaData = function () {
        const urlParams = new URLSearchParams(window.location.search);
        const cuadroId = urlParams.get('cuadro_id');
        console.log('loadEstadisticaData - cuadroId:', cuadroId);
        const cuadroInfoContainer = document.getElementById('cuadro-info-container');

        if (cuadroId && cuadroInfoContainer) {
            loadCuadroEspecifico(cuadroId);
        } else if (cuadroInfoContainer) {
            cuadroInfoContainer.innerHTML = `
                <div class="text-center py-5">
                    <i class="bi bi-info-circle text-muted" style="font-size: 3rem;"></i>
                    <h4 class="mt-3 text-muted">Selecciona un cuadro estadístico</h4>
                    <p class="text-muted">Para ver un cuadro estadístico específico, selecciona uno desde el catálogo.</p>
                    <div class="mt-4">
                        <button type="button" class="btn btn-success" onclick="SIGEMApp.loadContent('catalogo')">
                            <i class="bi bi-journal-text me-1"></i>Ir al Catálogo
                        </button>
                    </div>
                </div>
            `;
        }
    };

    // === FUNCION usada en la vista de estadística.blade ===
    function loadCuadroEspecifico(cuadroId) {
        const cuadroInfoContainer = document.getElementById('cuadro-info-container');
        if (!cuadroInfoContainer) {
            console.error('No se encontró el contenedor cuadro-info-container');
            return;
        }

        cuadroInfoContainer.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-success" role="status">
                    <span class="visually-hidden">Cargando cuadro...</span>
                </div>
                <h4 class="mt-3 text-muted">Cargando cuadro estadístico</h4>
                <p class="text-muted">ID: ${cuadroId}</p>
            </div>
        `;

        fetch(`${CONFIG.API_URL}/cuadro-data/${cuadroId}`)
            .then(response => response.json())
            .then(data => {
                console.log('Datos del cuadro cargados:', data);
                if (data.success && data.cuadro) {
                    cuadroInfoContainer.innerHTML = generateCuadroInfoHtml(data.cuadro, data.tema_info, data.subtema_info);
                } else {
                    cuadroInfoContainer.innerHTML = `
                        <div class="alert alert-warning text-center">
                            <i class="bi bi-exclamation-triangle fs-1"></i>
                            <h4 class="mt-3">Cuadro no encontrado</h4>
                            <p>El cuadro con ID <strong>${cuadroId}</strong> no existe o no está disponible.</p>
                            <small class="text-muted">Error: ${data.message || 'Desconocido'}</small>
                            <div class="mt-4">
                                <button type="button" class="btn btn-success" onclick="SIGEMApp.loadContent('catalogo')">
                                    <i class="bi bi-journal-text me-1"></i>Volver al Catálogo
                                </button>
                            </div>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error cargando cuadro específico:', error);
                cuadroInfoContainer.innerHTML = `
                    <div class="alert alert-danger text-center">
                        <i class="bi bi-exclamation-circle fs-1"></i>
                        <h4 class="mt-3">Error al cargar cuadro</h4>
                        <p>No se pudo cargar el cuadro estadístico.</p>
                        <small class="text-muted">Error: ${error.message}</small>
                        <div class="mt-4">
                            <button type="button" class="btn btn-success" onclick="SIGEMApp.loadContent('catalogo')">
                                <i class="bi bi-journal-text me-1"></i>Volver al Catálogo
                            </button>
                        </div>
                    </div>
                `;
            });
    }


    // === FUNCIONES DE GENERACIÓN DE HTML ===

    // === FUNCION usada en la vista de catálogo.blade ===
    // Esta funcion se usa para generar la estructura del índice de temas y subtemas en catalogo.blade, funciones de focusEnTema y focusEnSubtema son pertinentes a esta función
    function generateEstructuraIndice(temasDetalle) {
        let estructura = `<div style="font-size: 12px; overflow-y: auto;" id="indice-container">
            <p class="text-center mb-3"><strong>Son 6 temas principales y a cada uno le corresponden diferentes subtemas en donde encontramos los cuadros estadísticos</strong></p>
    `;
        const colores = [
            'background-color: #8FBC8F; color: white;',
            'background-color: #87CEEB; color: white;',
            'background-color: #DDA0DD; color: white;',
            'background-color: #F0E68C; color: black;',
            'background-color: #FFA07A; color: white;',
            'background-color: #98FB98; color: black;'
        ];

        temasDetalle.forEach((tema, index) => {
            const colorStyle = colores[index % colores.length];
            /*estructura += `
                <div class="mb-2">
                    <div class="p-2 rounded-top" style="${colorStyle}">
                        <strong>${tema.tema_titulo}</strong>
                    </div>
                    <div class="border rounded-bottom p-2 bg-light">`;*/

            estructura += `
            <div class="mb-3 indice-tema-container" style="border: 1px solid #ddd;">
                
                <div class="text-center text-white fw-bold py-2 indice-tema-header" 
                     style="${colorStyle} cursor: pointer; transition: all 0.3s ease;"
                     onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(0,0,0,0.2)';"
                     onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';"
                     onclick="SIGEMApp.focusEnTema(${tema.orden_indice}); return false;">
                    ${tema.orden_indice}. ${tema.tema_titulo.toUpperCase()}
                </div>
                
            <div style="background-color: white;">
            `;

            if (tema.subtemas && tema.subtemas.length > 0) {
                tema.subtemas.forEach(subtema => {
                    /*estructura += `
                        <div class="mb-1">
                            <a href="#" onclick="SIGEMApp.focusEnSubtema(${tema.tema_id}, ${subtema.orden_indice}); return false;" class="text-decoration-none text-dark">
                                <i class="bi bi-arrow-right-circle me-1"></i>${subtema.subtema_titulo}
                            </a>
                        </div>`;*/

                    estructura += `
                    <div class="d-flex border-bottom indice-subtema-row" 
                        style="${colorStyle} cursor: pointer; transition: all 0.3s ease;"
                        onmouseover="this.style.backgroundColor='#e8f4f8'; this.style.transform='translateX(5px)';"
                        onmouseout="this.style.backgroundColor='${colorStyle === 'background-color: #f8f9fa;' ? '#f8f9fa' : 'white'}'; this.style.transform='translateX(0)';"
                        onclick="SIGEMApp.focusEnSubtema(${tema.tema_id}, ${subtema.orden_indice}); return false;">
                        <div class="px-1 py-1 text-center fw-bold" style="min-width: 60px; border-right: 1px solid #ddd;">
                            ${subtema.clave_subtema || tema.clave_tema || 'N/A'} 
                        </div>
                        <div class="px-2 py-2 flex-grow-1">
                            ${subtema.subtema_titulo}
                        </div>
                    </div>`;
                    
                });
            } else {
                estructura += `<div class="fst-italic">Sin subtemas disponibles</div>`;
            }
            estructura += `</div></div>`;
        });
        estructura += `</div>`;
        return estructura;
    }

    // === FUNCION usada en la vista de catálogo.blade ===
    // FUNCIÓN que imprime la lista de cuadros estadísticos por tema y subtema en la vista de catálogo.blade
    function generateListaCuadros(cuadrosEstadisticos) {
        if (!cuadrosEstadisticos || cuadrosEstadisticos.length === 0) {
            return '<div class="alert alert-warning">No hay cuadros estadísticos disponibles</div>';
        }

        const cuadrosOrganizados = organizarCuadrosPorTema(cuadrosEstadisticos);
        let html = `<div style="overflow-y: auto;" id="cuadros-container">`;
        const colores = [
            'background-color: #8FBC8F; color: white;',
            'background-color: #87CEEB; color: white;',
            'background-color: #DDA0DD; color: white;',
            'background-color: #F0E68C; color: black;',
            'background-color: #FFA07A; color: white;',
            'background-color: #98FB98; color: black;'
        ];

        Object.keys(cuadrosOrganizados).forEach((temaKey, indexTema) => {
            const temaData = cuadrosOrganizados[temaKey];
            const colorStyle = colores[indexTema % colores.length];
            html += `
                <div class="mb-4" id="tema-cuadros-${temaData.tema_info.tema_id}">
                    <div class="p-2 rounded-top" style="${colorStyle}">
                        <h5 class="mb-0">${temaData.tema_info.tema_titulo}</h5>
                    </div>
                    <div class="border rounded-bottom">`;

            Object.keys(temaData.subtemas).forEach(subtemaKey => {
                const subtemaData = temaData.subtemas[subtemaKey];
                html += `
                    <div class="border-bottom p-3" id="subtema-cuadros-${temaData.tema_info.tema_id}-${subtemaData.subtema_info.orden_indice}">
                        <h6 class="text-primary">${subtemaData.subtema_info.subtema_titulo}</h6>
                        <div class="row g-3">`;

                subtemaData.cuadros.forEach(cuadro => {
                    html += `
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body d-flex flex-column">
                                    <h6 class="card-title">${cuadro.titulo}</h6>
                                    <p class="card-text flex-grow-1"><small class="text-muted">${cuadro.codigo_cuadro}</small></p>
                                    <button class="btn btn-success mt-auto" onclick="SIGEMApp.verCuadro(${cuadro.cuadro_id}, '${cuadro.codigo_cuadro}')">
                                        <i class="bi bi-table me-1"></i>Ver Cuadro
                                    </button>
                                </div>
                            </div>
                        </div>`;
                });
                html += `</div></div>`;
            });
            html += `</div></div>`;
        });
        html += `</div>`;
        return html;
    }

    // === FUNCION usada en la vista de cartografía.blade ===
    // Funcion que carga los datos del catálogo en la vista de catálogo.blade
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
                                                  onload="console.log('Imagen cargada: ${mapa.icono}')"
                                                  onerror="console.error('Error cargando imagen: ${mapa.icono}'); this.style.display='none'; this.parentNode.classList.add('image-error');"
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
    
        /*
        let html = `<div class="mb-3"><div class="alert alert-info d-flex align-items-center"><i class="bi bi-info-circle me-2"></i><strong>Mapas disponibles: ${data.total_mapas || 0}</strong></div></div>`;
        if (data.mapas && data.mapas.length > 0) {
            html += `<div class="row g-4">`;
            data.mapas.forEach(mapa => {
                const imageUrl = mapa.url_imagen ? `${CONFIG.BASE_URL}/${mapa.url_imagen}` : getImagePlaceholder(mapa);
                html += `
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm">
                            <div style="height: 200px; display: flex; align-items: center; justify-content: center; background-color: #f8f9fa;">
                                ${imageUrl}
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">${mapa.titulo}</h5>
                                <p class="card-text flex-grow-1">${mapa.descripcion || 'No hay descripción disponible para este mapa.'}</p>
                                <a href="${CONFIG.BASE_URL}/${mapa.url_documento}" target="_blank" class="btn btn-success mt-auto">
                                    <i class="bi bi-file-earmark-pdf me-1"></i>Ver PDF
                                </a>
                            </div>
                        </div>
                    </div>`;
            });
            html += `</div>`;
        } else {
            html += `<div class="row"><div class="col-12"><div class="alert alert-warning text-center"><i class="bi bi-exclamation-triangle fs-1"></i><h4 class="mt-3">No hay mapas disponibles</h4><p class="mb-0">Actualmente no hay mapas configurados en el sistema.</p></div></div></div>`;
        }
        return html;*/
    }

    // === FUNCION usada en la vista de cartografía.blade ===
    // FUNCIÓN AUXILIAR de mapas: Genera el HTML del placeholder de imagen
    function getImagePlaceholder(mapa) {
        return `<div class="mapa-image-placeholder"><i class="bi bi-image"></i><h5>${mapa.nombre_mapa || 'Mapa'}</h5><p>${mapa.icono ? 'Imagen no disponible' : 'Sin imagen configurada'}</p>${mapa.enlace ? `<small class="text-primary"><i class="bi bi-cursor-fill"></i>Haz clic para ver mapa</small>` : `<small class="text-muted">Mapa no disponible</small>`}</div>`;
    }

    // === FUNCION usada en la vista de estadística.blade ===
    // NUEVA FUNCIÓN: Generar HTML para mostrar información del cuadro, es usada en loadCuadroEspecifico
    function generateCuadroInfoHtml(cuadro, temaInfo, subtemaInfo) {
        return `
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <div class="row align-items-center">
                        <div class="col-10">
                            <h4 class="mb-0">${cuadro.titulo}</h4>
                            <small>${cuadro.codigo_cuadro} | ${temaInfo?.tema_titulo || 'Tema no disponible'} / ${subtemaInfo?.subtema_titulo || 'Subtema no disponible'}</small>
                        </div>
                        <div class="col-2 text-end">
                            <button class="btn btn-light btn-sm" onclick="SIGEMApp.loadContent('catalogo')">
                                <i class="bi bi-arrow-left-circle"></i> Volver
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <p>${cuadro.descripcion}</p>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-1"></i>
                        <strong>Nota:</strong> Este cuadro estadístico se presenta únicamente con fines ilustrativos. Para obtener datos actualizados y completos, se recomienda consultar directamente las fuentes oficiales del Instituto Municipal de Investigación y Planeación.
                    </div>
                    <div class="text-center my-4">
                        <img src="https://placehold.co/600x400/cccccc/969696?text=Gr%C3%A1fico+Estad%C3%ADstico" alt="Gráfico del cuadro ${cuadro.codigo_cuadro}" class="img-fluid rounded shadow-sm">
                    </div>
                    <div class="text-center mt-4">
                        <a href="${CONFIG.BASE_URL}/descargar-cuadro/${cuadro.cuadro_id}" class="btn btn-success">
                            <i class="bi bi-download me-1"></i>Descargar Datos (CSV)
                        </a>
                        <button class="btn btn-outline-secondary ms-2" onclick="alert('Funcionalidad de compartir no implementada aún')">
                            <i class="bi bi-share me-1"></i>Compartir
                        </button>
                        <button type="button" class="btn btn-outline-primary ms-2" onclick="window.print()">
                            <i class="bi bi-printer me-1"></i>Imprimir Información
                        </button>
                    </div>
                </div>
            </div>
        `;
    }

    // === EXPOSICIÓN GLOBAL DE FUNCIONES NECESARIAS ===
    window.SIGEMApp = SIGEMApp;
    window.focusEnTema = SIGEMApp.focusEnTema.bind(SIGEMApp);
    window.focusEnSubtema = SIGEMApp.focusEnSubtema.bind(SIGEMApp);
    window.verCuadro = SIGEMApp.verCuadro.bind(SIGEMApp);

    // === INICIALIZACIÓN ===
    document.addEventListener('DOMContentLoaded', function () {
        SIGEMApp.init();
    });

})(window, document);