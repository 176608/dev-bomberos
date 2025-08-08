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
            
            // Configurar URLs
            this.configureUrls();
            
            // Cachear elementos del DOM
            this.cacheElements();
            
            // Si hay alguna otra inicialización, la mantenemos
            this.setupOtherEvents();
            
            // Cargar contenido inicial
            this.loadInitialContent();
        },

        // Configurar URLs base dinámicamente
        configureUrls: function () {
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

        /* Enlazar eventos
        bindEvents: function () {
            this.elements.navLinks.forEach(link => {
                link.addEventListener('click', (e) => {
                    // Verificar si estamos en una ruta especial y el href no es '#'
                    const isDirectLink = link.getAttribute('href') !== '#';
                    if (isDirectLink) {
                        // No prevenir el comportamiento por defecto, permitir la navegación normal
                        return true;
                    }
                    
                    // Para rutas normales, usar el comportamiento AJAX existente
                    e.preventDefault();
                    const section = link.getAttribute('data-section');
                    this.loadContent(section);
                });
            });*/

            // Otros eventos que sean necesarios
        setupOtherEvents: function () {
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
            
            // Siempre actualizar la sección en la URL
            const newUrl = new URL(window.location);
            newUrl.searchParams.set('section', section);
            window.history.replaceState({}, '', newUrl);
            console.log(`Sección actualizada a: ${section}`);
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
            
            // Funciones importantes para la navegación
            this.saveCurrentSection(section); // PRIMERO actualizar la sección
            this.cleanUrlParameters(section); // DESPUÉS limpiar parámetros extra
            this.updateActiveMenu(section);
            this.showLoading(section);

            const url = `${CONFIG.PARTIALS_URL}/${section}`;
            console.log(`URL completa: ${url}`);

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

        // NUEVA FUNCIÓN: Limpiar parámetros de URL según la sección
        cleanUrlParameters: function(section) {
            const currentUrl = new URL(window.location);
            let urlChanged = false;
            
            // Si NO es estadística, remover cuadro_id
            if (section !== 'estadistica' && currentUrl.searchParams.has('cuadro_id')) {
                currentUrl.searchParams.delete('cuadro_id');
                urlChanged = true;
                console.log(`Removiendo cuadro_id de URL - sección cambiada a: ${section}`);
            }
            
            // Remover otros parámetros específicos que no corresponden a la sección actual
            switch (section) {
                case 'estadistica':
                    // Mantener cuadro_id si existe, remover otros parámetros específicos si los hubiera
                    if (currentUrl.searchParams.has('tema_id')) {
                        currentUrl.searchParams.delete('tema_id');
                        urlChanged = true;
                    }
                    if (currentUrl.searchParams.has('modo')) {
                        currentUrl.searchParams.delete('modo');
                        urlChanged = true;
                    }
                    break;
                    
                case 'catalogo':
                case 'cartografia':
                case 'inicio':
                case 'productos':
                    // Estas secciones no necesitan parámetros específicos
                    if (currentUrl.searchParams.has('tema_id')) {
                        currentUrl.searchParams.delete('tema_id');
                        urlChanged = true;
                    }
                    if (currentUrl.searchParams.has('modo')) {
                        currentUrl.searchParams.delete('modo');
                        urlChanged = true;
                    }
                    break;
            }
            
            // Actualizar URL solo si hubo cambios en parámetros extra
            if (urlChanged) {
                window.history.replaceState({}, '', currentUrl);
                console.log(`Parámetros limpiados para sección ${section}:`, currentUrl.toString());
            }
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
                case 'estadistica': // Carga datos para estadistica.blade
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

        // === FUNCION usada en la vista de estadistica_temas_con_subtemas.blade ===
        verCuadro: function (cuadroId, codigo) {
            console.log(`sigem: Inicializando modal de cuadro: ID=${cuadroId}, Código=${codigo}`);
            
            // Disparar un evento personalizado que será capturado en el blade
            const evento = new CustomEvent('verCuadroEstadistico', {
                detail: {
                    cuadroId: cuadroId,
                    codigo: codigo
                }
            });
            document.dispatchEvent(evento);
        },

        // Reemplazar el método openConsultaExpress con esta versión más simple
        openConsultaExpress: function() {            
            // Verificar si ya existe una instancia del modal
            const consultaExpressModal = document.getElementById('consultaExpressModal');
            
            if (!consultaExpressModal) {
                console.error('Modal no encontrado en el DOM');
                alert('No se pudo encontrar el modal de Consulta Express.');
                return;
            }
            
            // Abrir modal usando Bootstrap
            try {
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    const modal = new bootstrap.Modal(consultaExpressModal);
                    modal.show();
                    
                    // Verificar si necesitamos inicializar los eventos
                    if (!this.consultaExpressInitialized) {
                        this.initConsultaExpress();
                    }
                } else {
                    console.error('Bootstrap no está disponible');
                    // Intentar con jQuery si está disponible
                    if (typeof $ !== 'undefined' && $.fn.modal) {
                        $(consultaExpressModal).modal('show');
                        
                        if (!this.consultaExpressInitialized) {
                            this.initConsultaExpress();
                        }
                    } else {
                        alert('No se pudo abrir el modal. Bootstrap no está disponible.');
                    }
                }
            } catch (error) {
                console.error('Error al abrir el modal:', error);
                alert('Error al abrir el modal: ' + error.message);
            }
        },

        // Función para inicializar los eventos del modal
        initConsultaExpress: function() {            
            // Marcar como inicializado para no repetir
            this.consultaExpressInitialized = true;
            
            // Elementos DOM
            const temaSelect = document.getElementById('ce_tema_select_modal');
            const subtemaSelect = document.getElementById('ce_subtema_select_modal');
            const contenidoContainer = document.getElementById('ce_contenido_container_modal');
            
            if (!temaSelect || !subtemaSelect || !contenidoContainer) {
                console.error('Faltan elementos DOM necesarios para Consulta Express');
                return;
            }
            
            // Función para mostrar loader
            function showLoader() {
                contenidoContainer.innerHTML = `
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-2">Cargando...</p>
                    </div>
                `;
            }
            
            // Cargar subtemas cuando cambia el tema
            temaSelect.addEventListener('change', function() {
                const temaId = this.value;
                
                if (temaId) {
                    // Deshabilitar el selector de subtemas mientras se cargan
                    subtemaSelect.disabled = true;
                    subtemaSelect.innerHTML = '<option value="">Cargando subtemas...</option>';
                    //consultarBtn.disabled = true;
                    
                    // Realizar petición fetch
                    fetch(`${CONFIG.BASE_URL}/ajax/consulta-express/subtemas/${temaId}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`Error en la respuesta: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            // Limpiar selector de subtemas
                            subtemaSelect.innerHTML = '<option value="">Seleccione un subtema...</option>';
                            
                            if (data.success && data.subtemas && data.subtemas.length > 0) {
                                // Añadir opciones de subtemas
                                data.subtemas.forEach(function(subtema) {
                                    const option = document.createElement('option');
                                    option.value = subtema.ce_subtema_id;
                                    option.textContent = subtema.ce_subtema;
                                    subtemaSelect.appendChild(option);
                                });
                                
                                // Habilitar selector y botón
                                subtemaSelect.disabled = false;
                                
                                // Limpiar contenido si había algo mostrado
                                contenidoContainer.innerHTML = `
                                    <div class="text-center text-muted py-5">
                                        <i class="bi bi-info-circle fs-2"></i>
                                        <p class="mt-2">Seleccione un subtema para ver la información</p>
                                    </div>
                                `;
                            } else {
                                subtemaSelect.innerHTML = '<option value="">No hay subtemas disponibles</option>';
                                //consultarBtn.disabled = true;
                                
                                contenidoContainer.innerHTML = `
                                    <div class="alert alert-warning">No se encontraron subtemas para este tema.</div>
                                `;
                            }
                        })
                        .catch(error => {
                            console.error('Error al cargar subtemas:', error);
                            subtemaSelect.innerHTML = '<option value="">Error al cargar subtemas</option>';
                            subtemaSelect.disabled = true;
                            //consultarBtn.disabled = true;
                        });
                } else {
                    // Resetear subtemas y contenido
                    subtemaSelect.innerHTML = '<option value="">Primero seleccione un tema</option>';
                    subtemaSelect.disabled = true;
                    
                    contenidoContainer.innerHTML = `
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-info-circle fs-2"></i>
                            <p class="mt-2">Seleccione un tema y subtema para ver la información</p>
                        </div>
                    `;
                }
            });
            
            // Cuando cambia el subtema - habilitar botón
            subtemaSelect.addEventListener('change', function() {
                const subtemaId = subtemaSelect.value;
                
                if (subtemaId) {
                    showLoader();
                    
                    fetch(`${CONFIG.BASE_URL}/ajax/consulta-express/contenido/${subtemaId}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`Error en la respuesta: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success && data.contenido) {
                                // Mostrar contenido
                                contenidoContainer.innerHTML = data.contenido.ce_contenido;
                                
                                // Si hay un elemento de metadata
                                const metadataDiv = document.getElementById('ce_metadata_modal');
                                const fechaActualizacion = document.getElementById('ce_fecha_actualizacion_modal');
                                
                                if (metadataDiv && fechaActualizacion) {
                                    fechaActualizacion.textContent = data.actualizado;
                                    metadataDiv.style.display = 'block';
                                }
                            } else {
                                contenidoContainer.innerHTML = '<div class="alert alert-warning">No se encontró contenido para el subtema seleccionado.</div>';
                            }
                        })
                        .catch(error => {
                            console.error('Error al cargar contenido:', error);
                            contenidoContainer.innerHTML = `

                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                Error al cargar contenido: ${error.message}
                            </div>
                    `;
                        });
                }
            });
        },

        // Función para cargar el modal dinámicamente si no está presente
        loadModalConsultaExpress: function() {
            console.log('Intentando cargar modal de Consulta Express');
            
            // Crear un contenedor temporal donde cargar el partial
            const tempContainer = document.createElement('div');
            tempContainer.style.display = 'none';
            document.body.appendChild(tempContainer);
            
            // Cargar el partial via fetch
            fetch(`${CONFIG.BASE_URL}/partial/consulta-express`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.text();
                })
                .then(html => {
                    tempContainer.innerHTML = html;
                    
                    // Mover el modal al body
                    const modalElement = tempContainer.querySelector('#consultaExpressModal');
                    if (modalElement) {
                        document.body.appendChild(modalElement);                        
                        // Abrir el modal
                        this.openConsultaExpress();
                    } else {
                        console.error('Modal no encontrado en el partial cargado');
                    }
                    
                    // Eliminar el contenedor temporal
                    document.body.removeChild(tempContainer);
                })
                .catch(error => {
                    console.error('Error al cargar el modal:', error);
                    alert('No se pudo cargar la consulta express. Por favor, intente nuevamente.');
                });
        },
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
                    // En la función loadCuadroEspecifico o donde se cargan los datos
                    document.dispatchEvent(new CustomEvent('cuadroDataLoaded', {
                        detail: {
                            cuadro: data.cuadro,
                            subtema_info: data.subtema_info,
                            tema_info: data.tema_info
                        }
                    }));
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
        'background-color: #8FBC8F; color: black;',
        'background-color: #87CEEB; color: black;',
        'background-color: #DDA0DD; color: black;',
        'background-color: #F0E68C; color: black;',
        'background-color: #FFA07A; color: black;',
        'background-color: #98FB98; color: black;'
    ];

    temasDetalle.forEach((tema, index) => {
        const colorStyle = colores[index % colores.length];
        const numeroTema = index + 1;

        estructura += `
            <div class="mb-3 indice-tema-container">
                
                <div class="text-center fw-bold py-2 indice-tema-header" 
                     style="${colorStyle} cursor: pointer; transition: all 0.3s ease;" 
                     data-tema="${numeroTema}"
                     onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(0,0,0,0.2)';"
                     onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';"
                     onclick="SIGEMApp.focusEnTema(${tema.tema_id}); return false;">
                    ${tema.orden_indice}. ${tema.tema_titulo.toUpperCase()}
                </div>
                
                <div style="background-color: white;">
        `;

        if (tema.subtemas && tema.subtemas.length > 0) {
            tema.subtemas.forEach((subtema, subtemaIndex) => {
                const bgColor = subtemaIndex % 2 === 0 ? 'background-color: #f8f9fa;' : 'background-color: white;';
                const ordenSubtema = subtema.orden_indice || subtemaIndex;
                
                estructura += `
                    <div class="d-flex border-bottom indice-subtema-row" 
                         style="${bgColor} cursor: pointer; transition: all 0.3s ease;"
                         data-tema="${tema.tema_id}" 
                         data-subtema="${ordenSubtema}"
                         onmouseover="this.style.backgroundColor='#e8f4f8'; this.style.transform='translateX(5px)';"
                         onmouseout="this.style.backgroundColor='${bgColor === 'background-color: #f8f9fa;' ? '#f8f9fa' : 'white'}'; this.style.transform='translateX(0)';"
                         onclick="SIGEMApp.focusEnSubtema(${tema.tema_id}, ${ordenSubtema}); return false;">
                        <div class="px-1 py-1 text-center fw-bold" style="min-width: 60px; border-right: 1px solid #ddd;">
                            ${subtema.clave_subtema || tema.clave_tema || 'N/A'} 
                        </div>
                        <div class="px-2 py-2 flex-grow-1">
                            ${subtema.subtema_titulo}
                        </div>
                    </div>
                `;
            });
        } else {
            estructura += `
                <div class="px-2 py-2 text-muted">
                    <em>Sin subtemas disponibles</em>
                </div>
            `;
        }

        estructura += `
                </div>
            </div>
        `;
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
        
        // Colores para los headers de temas (igual que la estructura de índice)
        const colores = [
            'background-color: #8FBC8F; color: black;',
            'background-color: #87CEEB; color: black;',
            'background-color: #DDA0DD; color: black;',
            'background-color: #F0E68C; color: black;',
            'background-color: #FFA07A; color: black;',
            'background-color: #98FB98; color: black;'
        ];

        Object.keys(cuadrosOrganizados).forEach((temaKey, indexTema) => {
            const temaData = cuadrosOrganizados[temaKey];
            const colorStyle = colores[indexTema % colores.length];
            
            html += `
                <div class="mb-4" style="border: 1px solid #ddd; border-radius: 5px;" id="tema-cuadros-${temaData.tema_info.tema_id}">
                    
                    <div class="text-center fw-bold py-2" style="${colorStyle}">
                        ${temaData.tema_info.orden_indice || (indexTema + 1)}. ${temaData.tema_info.tema_titulo.toUpperCase()}
                    </div>
                    
                    
                    <div style="background-color: white;">
            `;

            Object.keys(temaData.subtemas).forEach((subtemaKey, subtemaIndex) => {
                const subtemaData = temaData.subtemas[subtemaKey];
                const ordenSubtema = subtemaData.subtema_info.orden_indice || subtemaIndex;
                
                // Header del subtema con ID para focus
                html += `
                    <div class="px-1 py-1 bg-light border-bottom fw-bold" style="font-size: 14px;" id="subtema-cuadros-${temaData.tema_info.tema_id}-${ordenSubtema}">
                        ${subtemaData.subtema_info.subtema_titulo}
                    </div>
                `;

                // Cuadros del subtema
                if (subtemaData.cuadros && subtemaData.cuadros.length > 0) {
                    subtemaData.cuadros.forEach((cuadro, cuadroIndex) => {
                        const bgColor = cuadroIndex % 2 === 0 ? 'background-color: #f8f9fa;' : 'background-color: white;';

                        html += `
                            <div class="d-flex align-items-center border-bottom py-2 px-3" style="${bgColor}">
                                <div class="me-3" style="min-width: 80px;">
                                    ${cuadro.codigo_cuadro || 'N/A'}
                                </div>
                                <div class="flex-grow-1 me-3" style="font-size: 12px;">
                                    <div class="fw-bold">${cuadro.cuadro_estadistico_titulo || 'Sin título'}</div>
                                    ${cuadro.cuadro_estadistico_subtitulo ? `<small class="text-muted">${cuadro.cuadro_estadistico_subtitulo}</small>` : ''}
                                </div>
                            </div>
                        `;
                    });
                } else {
                    html += `
                        <div class="px-3 py-2 text-muted">
                            <em>Sin cuadros estadísticos en este subtema</em>
                        </div>
                    `;
                }
            });

            html += `
                    </div>
                </div>
            `;
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
    
    }

    // === FUNCION usada en la vista de cartografía.blade ===
    // FUNCIÓN AUXILIAR de mapas: Genera el HTML del placeholder de imagen
    function getImagePlaceholder(mapa) {
        return `<div class="mapa-image-placeholder"><i class="bi bi-image"></i><h5>${mapa.nombre_mapa || 'Mapa'}</h5><p>${mapa.icono ? 'Imagen no disponible' : 'Sin imagen configurada'}</p>${mapa.enlace ? `<small class="text-primary"><i class="bi bi-cursor-fill"></i>Haz clic para ver mapa</small>` : `<small class="text-muted">Mapa no disponible</small>`}</div>`;
    }

    // === FUNCION a borrar ===
    // NUEVA FUNCIÓN: Generar HTML para mostrar información del cuadro, es usada en loadCuadroEspecifico
    /*function generateCuadroInfoHtml(cuadro, temaInfo, subtemaInfo) {
        return `
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <div class="row align-items-center">
                        <div class="col-10">
                            <h4 class="mb-0">${cuadro.cuadro_estadistico_titulo}</h4>
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

                    <div class="text-center my-4">
                        Gráfico del cuadro ${cuadro.codigo_cuadro}"
                    </div>

                    <div class="mb-3">${cuadro.pie_pagina}</div>

                    <div class="text-center mt-4">
                        <a href="${CONFIG.BASE_URL}/descargar-cuadro/${cuadro.cuadro_estadistico_id}" class="btn btn-success">
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
    }*/

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