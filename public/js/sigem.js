// resources/js/sigem_optimized.js
// (Basado en Pasted_Text_1754075838952.txt, refactorizado)

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
            console.log('Inicializando SIGEMApp (Optimizado)...');
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
            console.log(`URL completa: ${url}`); // Manteniendo este log de info

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
                case 'cartografia':
                    this.loadMapasData();
                    break;
                case 'catalogo':
                    this.loadCatalogoData();
                    break;
                case 'estadistica':
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

        verCuadro: function (cuadroId, codigo) {
            console.log(`Abriendo cuadro: ID=${cuadroId}, Código=${codigo}`);
            const url = `${CONFIG.BASE_URL}?section=estadistica&cuadro_id=${cuadroId}`;
            console.log(`Usando URL: ${url}`);
            window.open(url, '_blank');
        }
    };

    // === FUNCIONES DE UTILIDAD ===

    function sincronizarAlturas() {
        const indiceContainer = document.getElementById('indice-container');
        const cuadrosContainer = document.getElementById('cuadros-container');

        if (indiceContainer && cuadrosContainer) {
            indiceContainer.style.height = 'auto';
            cuadrosContainer.style.height = 'auto';

            const indiceHeight = indiceContainer.offsetHeight;
            const cuadrosHeight = cuadrosContainer.offsetHeight;
            const maxHeight = Math.max(indiceHeight, cuadrosHeight);

            if (maxHeight > 0) {
                indiceContainer.style.height = `${maxHeight}px`;
                cuadrosContainer.style.height = `${maxHeight}px`;
            }
        }
    }

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

    function getImagePlaceholder(width = 200, height = 150, text = 'Mapa') {
        return `https://placehold.co/${width}x${height}/cccccc/969696?text=${encodeURIComponent(text)}`;
    }

    function getImagePlaceholderEscaped(width = 200, height = 150, text = 'Mapa') {
        return getImagePlaceholder(width, height, text).replace(/"/g, '&quot;');
    }


    // === FUNCIONES DE CARGA DE DATOS POR SECCIÓN ===

    SIGEMApp.loadMapasData = function () {
        const mapasContainer = document.getElementById('mapas-container');
        if (!mapasContainer) return;

        mapasContainer.innerHTML = '<div class="text-center py-3"><div class="spinner-border text-success" role="status"><span class="visually-hidden">Cargando...</span></div></div>';

        fetch(`${CONFIG.API_URL}/mapas`)
            .then(response => response.json())
            .then(data => {
                console.log('DATOS MAPAS RECIBIDOS:', data);
                if (data.success) {
                    mapasContainer.innerHTML = generateMapasHtml(data);
                } else {
                    mapasContainer.innerHTML = `<div class="alert alert-danger"><i class="bi bi-exclamation-circle"></i> Error al cargar mapas: ${data.message}</div>`;
                }
            })
            .catch(error => {
                console.error('Error cargando mapas:', error);
                mapasContainer.innerHTML = '<div class="alert alert-danger">Error de conexión</div>';
            });
    };

    SIGEMApp.loadCatalogoData = function () {
        const indiceContainer = document.getElementById('indice-container');
        const cuadrosContainer = document.getElementById('cuadros-container');

        if (indiceContainer) indiceContainer.innerHTML = '<div class="text-center py-3"><div class="spinner-border text-success" role="status"><span class="visually-hidden">Cargando índice...</span></div></div>';
        if (cuadrosContainer) cuadrosContainer.innerHTML = '<div class="text-center py-3"><div class="spinner-border text-success" role="status"><span class="visually-hidden">Cargando cuadros...</span></div></div>';

        fetch(`${CONFIG.API_URL}/catalogo`)
            .then(response => response.json())
            .then(data => {
                console.log('Response completa:', data); // Log de info solicitado
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

    function generateEstructuraIndice(temasDetalle) {
        let estructura = `<div style="font-size: 12px; overflow-y: auto;" id="indice-container">`;
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
            estructura += `
                <div class="mb-2">
                    <div class="p-2 rounded-top" style="${colorStyle}">
                        <strong>${tema.tema_titulo}</strong>
                    </div>
                    <div class="border rounded-bottom p-2 bg-light">`;
            if (tema.subtemas && tema.subtemas.length > 0) {
                tema.subtemas.forEach(subtema => {
                    estructura += `
                        <div class="mb-1">
                            <a href="#" onclick="SIGEMApp.focusEnSubtema(${tema.tema_id}, ${subtema.orden_indice}); return false;" class="text-decoration-none text-dark">
                                <i class="bi bi-arrow-right-circle me-1"></i>${subtema.subtema_titulo}
                            </a>
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

    function generateMapasHtml(data) {
        let html = `<div class="mb-3"><div class="alert alert-info d-flex align-items-center"><i class="bi bi-info-circle me-2"></i><div>Los mapas temáticos son documentos geográficos que representan información estadística mediante el uso de colores, símbolos y otros recursos cartográficos.</div></div></div><div class="row g-4">`;
        if (data.mapas && data.mapas.length > 0) {
            data.mapas.forEach(mapa => {
                const imageUrl = mapa.url_imagen ? `${CONFIG.BASE_URL}/${mapa.url_imagen}` : getImagePlaceholder(400, 300, mapa.titulo);
                html += `
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm">
                            <img src="${imageUrl}" class="card-img-top" alt="${mapa.titulo}" style="height: 200px; object-fit: cover;">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">${mapa.titulo}</h5>
                                <p class="card-text flex-grow-1">${mapa.descripcion}</p>
                                <a href="${CONFIG.BASE_URL}/${mapa.url_documento}" target="_blank" class="btn btn-success mt-auto">
                                    <i class="bi bi-file-earmark-pdf me-1"></i>Ver PDF
                                </a>
                            </div>
                        </div>
                    </div>`;
            });
        } else {
            html += `<div class="col-12"><div class="alert alert-warning">No hay mapas disponibles actualmente.</div></div>`;
        }
        html += `</div>`;
        return html;
    }

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
                        <img src="${getImagePlaceholder(600, 400, 'Gráfico Estadístico')}" alt="Gráfico del cuadro ${cuadro.codigo_cuadro}" class="img-fluid rounded shadow-sm">
                    </div>
                    <div class="text-center mt-4">
                        <a href="${CONFIG.BASE_URL}/descargar-cuadro/${cuadro.cuadro_id}" class="btn btn-success">
                            <i class="bi bi-download me-1"></i>Descargar Datos (CSV)
                        </a>
                        <button class="btn btn-outline-secondary ms-2" onclick="alert('Funcionalidad de compartir no implementada aún')">
                            <i class="bi bi-share me-1"></i>Compartir
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