// === FUNCIONES GLOBALES ORIGINALES (únicas, no duplicadas) ===
function focusEnTema(numeroTema) {
    console.log(`Focus en tema: ${numeroTema}`);
    
    const temaElement = document.getElementById(`tema-cuadros-${numeroTema}`);
    const cuadrosContainer = document.getElementById('cuadros-container');
    
    if (temaElement && cuadrosContainer) {
        // Remover highlights previos
        document.querySelectorAll('.highlight-focus').forEach(el => {
            el.classList.remove('highlight-focus');
        });
        
        // Scroll al tema en el contenedor de cuadros
        cuadrosContainer.scrollTo({
            top: temaElement.offsetTop - cuadrosContainer.offsetTop,
            behavior: 'smooth'
        });
        
        // Agregar highlight temporal
        temaElement.classList.add('highlight-focus');
        
        // Remover highlight después de 3 segundos
        setTimeout(() => {
            temaElement.classList.remove('highlight-focus');
        }, 3000);
    } else {
        console.warn(`No se encontró elemento para tema ${numeroTema}. TemaElement:`, temaElement, 'CuadrosContainer:', cuadrosContainer);
    }
}

function focusEnSubtema(numeroTema, ordenSubtema) {
    console.log(`Focus en subtema: Tema ${numeroTema}, Subtema ${ordenSubtema}`);
    
    const subtemaElement = document.getElementById(`subtema-cuadros-${numeroTema}-${ordenSubtema}`);
    const cuadrosContainer = document.getElementById('cuadros-container');
    
    if (subtemaElement && cuadrosContainer) {
        // Remover highlights previos
        document.querySelectorAll('.highlight-focus').forEach(el => {
            el.classList.remove('highlight-focus');
        });
        
        // Scroll al subtema en el contenedor de cuadros
        cuadrosContainer.scrollTo({
            top: subtemaElement.offsetTop - cuadrosContainer.offsetTop,
            behavior: 'smooth'
        });
        
        // Agregar highlight temporal
        subtemaElement.classList.add('highlight-focus');
        
        // Remover highlight después de 3 segundos
        setTimeout(() => {
            subtemaElement.classList.remove('highlight-focus');
        }, 3000);
    } else {
        console.warn(`No se encontró elemento para subtema ${numeroTema}-${ordenSubtema}. SubtemaElement:`, subtemaElement, 'CuadrosContainer:', cuadrosContainer);
    }
}

function verCuadro(cuadroId, codigo) {
    console.log(`Abriendo cuadro: ID=${cuadroId}, Código=${codigo}`);
    
    const baseUrl = window.SIGEM_BASE_URL || 
                   (window.location.pathname.includes('/m_aux/') ? '/m_aux/public/sigem' : '/sigem');
    
    // NUEVA FUNCIONALIDAD: Abrir sección estadística con cuadro específico
    const url = `${baseUrl}?section=estadistica&cuadro_id=${cuadroId}`;

    console.log(`Usando URL: ${url}`);

    window.open(url, '_blank');
}

// === FUNCIONES DE GENERACIÓN HTML (copiadas exactas del sigem_admin) ===

// FUNCIÓN EXACTA del sigem_admin que funciona
function generateEstructuraIndice(temasDetalle) {
    let estructura = `
        <div style="font-size: 12px; overflow-y: auto;" id="indice-container">
            <p class="text-center mb-3"><strong>Son 6 temas principales y a cada uno le corresponden diferentes subtemas en donde encontramos los cuadros estadísticos</strong></p>
    `;

    temasDetalle.forEach((tema, temaIndex) => {
        // Determinar el color del header basado en el número de tema
        const colores = [
            'background-color: #8FBC8F;', // Verde claro
            'background-color: #87CEEB;', // Azul cielo
            'background-color: #DDA0DD;', // Púrpura claro
            'background-color: #F0E68C;', // Amarillo claro
            'background-color: #FFA07A;', // Salmón
            'background-color: #98FB98;'  // Verde pálido
        ];
        
        const colorTema = colores[temaIndex % colores.length];
        const numeroTema = temaIndex + 1;

        estructura += `
            <div class="mb-3 indice-tema-container" style="border: 1px solid #ddd;">
                <!-- Header del tema -->
                <div class="text-center text-white fw-bold py-2 indice-tema-header" 
                     style="${colorTema} cursor: pointer; transition: all 0.3s ease;" 
                     data-tema="${numeroTema}"
                     onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(0,0,0,0.2)';"
                     onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';"
                     onclick="focusEnTema(${numeroTema});">
                    ${numeroTema}. ${tema.tema_titulo.toUpperCase()}
                </div>
                
                <div style="background-color: white;">
        `;

        if (tema.subtemas && tema.subtemas.length > 0) {
            tema.subtemas.forEach((subtema, subtemaIndex) => {
                // Alternar colores de fondo para las filas
                const bgColor = subtemaIndex % 2 === 0 ? 'background-color: #f8f9fa;' : 'background-color: white;';
                const ordenSubtema = subtema.orden_indice || subtemaIndex;
                
                estructura += `
                    <div class="d-flex border-bottom indice-subtema-row" 
                         style="${bgColor} cursor: pointer; transition: all 0.3s ease;"
                         data-tema="${numeroTema}" 
                         data-subtema="${ordenSubtema}"
                         onmouseover="this.style.backgroundColor='#e8f4f8'; this.style.transform='translateX(5px)';"
                         onmouseout="this.style.backgroundColor='${bgColor === 'background-color: #f8f9fa;' ? '#f8f9fa' : 'white'}'; this.style.transform='translateX(0)';"
                         onclick="focusEnSubtema(${numeroTema}, ${ordenSubtema});">
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

// FUNCIÓN EXACTA del sigem_admin que funciona
function generateListaCuadros(cuadrosEstadisticos) {
        if (!cuadrosEstadisticos || cuadrosEstadisticos.length === 0) {
            return '<div class="alert alert-warning">No hay cuadros estadísticos disponibles</div>';
        }

        // Organizar cuadros por tema y subtema
        const cuadrosOrganizados = organizarCuadrosPorTema(cuadrosEstadisticos);

        let html = `
            <div style="overflow-y: auto;" id="cuadros-container">
        `;

        // Colores para los headers de temas (igual que la estructura de índice)
        const colores = [
            'background-color: #8FBC8F; color: white;', // Verde claro
            'background-color: #87CEEB; color: white;', // Azul cielo
            'background-color: #DDA0DD; color: white;', // Púrpura claro
            'background-color: #F0E68C; color: black;', // Amarillo claro
            'background-color: #FFA07A; color: white;', // Salmón
            'background-color: #98FB98; color: black;'  // Verde pálido
        ];

        // Usar índice basado en el orden real
        let temaIndex = 0;
        Object.keys(cuadrosOrganizados).forEach((temaKey) => {
            const tema = cuadrosOrganizados[temaKey];
            const colorTema = colores[temaIndex % colores.length];
            
            // Usar orden_indice del tema para mostrar el número correcto
            const numeroTema = tema.orden_indice || (temaIndex + 1);
            const temaId = `tema-cuadros-${numeroTema}`;

            html += `
                <div class="mb-4" style="border: 1px solid #ddd; border-radius: 5px;" id="${temaId}">
                    <!-- Header del tema -->
                    <div class="text-center fw-bold py-2" style="${colorTema}">
                        ${numeroTema}. ${tema.nombre.toUpperCase()}
                    </div>
                    
                    <!-- Subtemas y cuadros -->
                    <div style="background-color: white;">
            `;

            Object.keys(tema.subtemas).forEach((subtemaKey, subtemaIndex) => {
                const subtema = tema.subtemas[subtemaKey];
                const ordenSubtema = subtema.orden_indice || subtemaIndex;
                const subtemaId = `subtema-cuadros-${numeroTema}-${ordenSubtema}`;


                // Header del subtema con ID para focus
                html += `
                    <div class="px-3 py-2 bg-light border-bottom fw-bold" style="font-size: 14px;" id="${subtemaId}">
                        <p class="fw-normal">${subtema.nombre}</p>
                    </div>
                `;

                // Cuadros del subtema
                if (subtema.cuadros && subtema.cuadros.length > 0) {
                    subtema.cuadros.forEach((cuadro, cuadroIndex) => {
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
                                <div>
                                    <a href="#" class="btn btn-sm btn-outline-success" 
                                       onclick="verCuadro(${cuadro.cuadro_estadistico_id}, '${cuadro.codigo_cuadro}')"
                                       title="Ver cuadro ${cuadro.codigo_cuadro}">
                                        <i class="bi bi-eye"></i>
                                    </a>
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
            
            temaIndex++;
        });

        html += `</div>`;
        return html;
    }

// FUNCIÓN EXACTA del sigem_admin que funciona
function sincronizarAlturas() {
    setTimeout(() => {
        const indiceContainer = document.getElementById('indice-container');
        const cuadrosContainer = document.getElementById('cuadros-container');
        
        if (indiceContainer && cuadrosContainer) {
            // 1. RESETEAR: Permitir que el índice tenga su altura natural
            indiceContainer.style.height = 'auto';
            cuadrosContainer.style.height = 'auto';
            
            // 2. MEDIR: La altura natural del contenido del índice
            const alturaIndice = indiceContainer.scrollHeight;
            
            // 3. APLICAR: La altura del índice al contenedor de cuadros
            // El índice mantiene su altura natural, los cuadros se ajustan a esa altura
            cuadrosContainer.style.height = alturaIndice + 'px';
            
            // 4. OPCIONAL: Aplicar altura mínima si el contenido es muy pequeño
            const alturaMinima = 300; // píxeles mínimos
            if (alturaIndice < alturaMinima) {
                const alturaFinal = alturaMinima + 'px';
                indiceContainer.style.height = alturaFinal;
                cuadrosContainer.style.height = alturaFinal;
            } else {
                // El índice mantiene altura automática, cuadros siguen al índice
                indiceContainer.style.height = 'auto';
                cuadrosContainer.style.height = alturaIndice + 'px';
            }
        }
    }, 100);
}

// FUNCIÓN EXACTA del sigem_admin que funciona
function organizarCuadrosPorTema(cuadrosEstadisticos) {
    const organizacion = {};

    cuadrosEstadisticos.forEach(cuadro => {
        // CORREGIR: La información del tema viene a través del subtema
        const subtemaInfo = cuadro.subtema || { subtema_id: 'sin_subtema', subtema_titulo: 'Sin Subtema', orden_indice: 0 };
        const temaInfo = subtemaInfo.tema || { tema_id: 'sin_tema', tema_titulo: 'Sin Tema Info', orden_indice: 0 };

        const temaKey = `tema_${temaInfo.tema_id}`;
        const subtemaKey = `subtema_${subtemaInfo.subtema_id}`;

        // Inicializar tema si no existe
        if (!organizacion[temaKey]) {
            organizacion[temaKey] = {
                nombre: temaInfo.tema_titulo || 'Sin Tema',
                clave: temaInfo.clave_tema || '',
                orden_indice: temaInfo.orden_indice || 0,
                subtemas: {}
            };
        }

        // Inicializar subtema si no existe
        if (!organizacion[temaKey].subtemas[subtemaKey]) {
            organizacion[temaKey].subtemas[subtemaKey] = {
                nombre: subtemaInfo.subtema_titulo || 'Sin Subtema',
                clave: subtemaInfo.clave_subtema || subtemaInfo.clave_efectiva || temaInfo.clave_tema || '',
                orden_indice: subtemaInfo.orden_indice || 0,
                subtema_info: subtemaInfo, // AGREGAR: Guardar info completa del subtema
                cuadros: []
            };
        }

        // Agregar cuadro al subtema
        organizacion[temaKey].subtemas[subtemaKey].cuadros.push(cuadro);
    });

    // AGREGAR: Ordenar por orden_indice
    const organizacionOrdenada = {};
    
    // 1. Ordenar temas por orden_indice
    const temasOrdenados = Object.keys(organizacion).sort((a, b) => {
        const ordenA = organizacion[a].orden_indice || 0;
        const ordenB = organizacion[b].orden_indice || 0;
        return ordenA - ordenB;
    });

    // 2. Para cada tema ordenado, ordenar sus subtemas
    temasOrdenados.forEach(temaKey => {
        const tema = organizacion[temaKey];
        
        organizacionOrdenada[temaKey] = {
            ...tema,
            subtemas: {}
        };

        // Ordenar subtemas por orden_indice
        const subtemasOrdenados = Object.keys(tema.subtemas).sort((a, b) => {
            const ordenA = tema.subtemas[a].orden_indice || 0;
            const ordenB = tema.subtemas[b].orden_indice || 0;
            return ordenA - ordenB;
        });

        // 3. Para cada subtema ordenado, ordenar sus cuadros CONSIDERANDO orden_indice del subtema
        subtemasOrdenados.forEach(subtemaKey => {
            const subtema = tema.subtemas[subtemaKey];
            
            organizacionOrdenada[temaKey].subtemas[subtemaKey] = {
                ...subtema,
                cuadros: subtema.cuadros.sort((a, b) => {
                    // NUEVA LÓGICA: Ordenar considerando orden_indice del subtema primero
                    return compararCodigosCuadro(
                        a.codigo_cuadro || '', 
                        b.codigo_cuadro || '',
                        a.subtema || subtema.subtema_info,
                        b.subtema || subtema.subtema_info
                    );
                })
            };
        });
    });

    return organizacionOrdenada;
}

// FUNCIÓN EXACTA del sigem_admin que funciona
function compararCodigosCuadro(codigoA, codigoB, subtemaInfoA, subtemaInfoB) {
    // 1. PRIMERO: Comparar por orden_indice del subtema
    const ordenSubtemaA = subtemaInfoA?.orden_indice || 0;
    const ordenSubtemaB = subtemaInfoB?.orden_indice || 0;
    
    if (ordenSubtemaA !== ordenSubtemaB) {
        return ordenSubtemaA - ordenSubtemaB;
    }

    // 2. SEGUNDO: Si tienen el mismo subtema, comparar numéricamente el tercer número
    function extraerNumero(codigo) {
        if (!codigo) return 0;
        
        const partes = codigo.split('.');
        if (partes.length >= 3) {
            // Obtener la parte después del segundo punto y convertir a número
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

// === RESTO DEL CÓDIGO EXISTENTE (sin duplicaciones) ===
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
        
        // ACTUALIZAR: Incluir 'inicio'
        if (urlSection && ['inicio', 'catalogo', 'estadistica', 'cartografia', 'productos'].includes(urlSection)) {
            return urlSection;
        }
        
        // 2. Verificar localStorage
        const storedSection = localStorage.getItem(STORAGE_KEY);
        // ACTUALIZAR: Incluir 'inicio'
        if (storedSection && ['inicio', 'catalogo', 'estadistica', 'cartografia', 'productos'].includes(storedSection)) {
            return storedSection;
        }
        
        // 3. CAMBIAR: Por defecto INICIO (en lugar de catálogo)
        return 'inicio';
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

    // FUNCIÓN: Cargar contenido (AGREGAR detección de cuadro_id)
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
                if (section === 'inicio') {
                    loadInicioData();
                } else if (section === 'cartografia') {
                    loadMapasData();
                } else if (section === 'catalogo') {
                    loadCatalogoData();
                } else if (section === 'estadistica') {
                    // NUEVA FUNCIONALIDAD: Cargar cuadro específico si viene en URL
                    loadEstadisticaData();
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

    // FUNCIÓN: Cargar datos de catálogo (REEMPLAZAR con la función del contexto)
    function loadCatalogoData() {
        const baseUrl = window.SIGEM_BASE_URL || 
                       (window.location.pathname.includes('/m_aux/') ? '/m_aux/public/sigem' : '/sigem');
        
        const cuadroInfoContainer = document.getElementById('cuadro-info-container');
        const infoCuadroByClick = document.getElementById('info_cuadro_by_click');
        
        if (!cuadroInfoContainer) {
            console.error('No se encontró el contenedor cuadro-info-container');
            return;
        }
        
        // Mostrar loading en el contenedor específico
        cuadroInfoContainer.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-success" role="status">
                    <span class="visually-hidden">Cargando cuadro...</span>
                </div>
                <h4 class="mt-3 text-muted">Cargando cuadro estadístico</h4>
                <p class="text-muted">ID: ${cuadroId}</p>
            </div>
        `;
        
        // Cargar datos del cuadro
        fetch(`${baseUrl}/cuadro-data/${cuadroId}`)
            .then(response => response.json())
            .then(data => {
                console.log('=== DATOS RAW DEL CATÁLOGO ===');
                console.log('Response completa:', data);
                console.log('Success:', data.success);
                console.log('Total temas:', data);
                console.log('Temas detalle:', data.temas_detalle);
                console.log('Cuadros estadísticos:', data.cuadros_estadisticos);
                console.log('=== FIN DATOS RAW ===');
                
                if (data.success) {
                    const indiceContainer = document.getElementById('indice-container');
                    const cuadrosContainer = document.getElementById('cuadros-container');
                    
                    if (indiceContainer && data.temas_detalle) {
                        indiceContainer.innerHTML = generateEstructuraIndice(data.temas_detalle);
                    }
                    
                    if (cuadrosContainer && data.cuadros_estadisticos) {
                        cuadrosContainer.innerHTML = generateListaCuadros(data.cuadros_estadisticos);
                    }
                    
                    // Sincronizar alturas después de cargar contenido
                    sincronizarAlturas();
                } else {
                    const indiceContainer = document.getElementById('indice-container');
                    const cuadrosContainer = document.getElementById('cuadros-container');
                    
                    if (indiceContainer) {
                        indiceContainer.innerHTML = `
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-circle"></i>
                                Error al cargar catálogo: ${data.message}
                            </div>
                        `;
                    }
                    
                    if (cuadrosContainer) {
                        cuadrosContainer.innerHTML = `
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-circle"></i>
                                Error al cargar cuadros estadísticos
                            </div>
                        `;
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const indiceContainer = document.getElementById('indice-container');
                const cuadrosContainer = document.getElementById('cuadros-container');
                
                if (indiceContainer) {
                    indiceContainer.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-circle"></i>
                            Error de conexión al cargar catálogo
                        </div>
                    `;
                }
                
                if (cuadrosContainer) {
                    cuadrosContainer.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-circle"></i>
                            Error de conexión al cargar cuadros
                        </div>
                    `;
                }
            });
    }

    // FUNCIÓN: Cargar datos de inicio
    function loadInicioData() {
        const baseUrl = window.SIGEM_BASE_URL || 
                       (window.location.pathname.includes('/m_aux/') ? '/m_aux/public/sigem' : '/sigem');
        
        fetch(`${baseUrl}/datos-inicio`)
            .then(response => response.json())
            .then(data => {
                console.log('Datos de inicio cargados:', data);
                
                if (data.success) {
                    // Actualizar estadísticas
                    if (data.estadisticas) {
                        const statTemas = document.getElementById('stat-temas');
                        const statSubtemas = document.getElementById('stat-subtemas');
                        const statCuadros = document.getElementById('stat-cuadros');
                        
                        if (statTemas) statTemas.textContent = data.estadisticas.total_temas || 0;
                        if (statSubtemas) statSubtemas.textContent = data.estadisticas.total_subtemas || 0;
                        if (statCuadros) statCuadros.textContent = data.estadisticas.total_cuadros || 0;
                    }
                } else {
                    //console.warn('Error en datos de inicio:', data.message);
                }
            })
            .catch(error => {
                console.error('Error cargando datos de inicio:', error);
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

    // Event listeners para navegación
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const section = this.getAttribute('data-section');
            loadContent(section);
        });
    });
    
    // Event listeners para botones de control (CORREGIR UBICACIÓN)
    document.addEventListener('click', function(e) {
        if (e.target.id === 'btn-expandir-todo' || e.target.closest('#btn-expandir-todo')) {
            expandirTodo();
        }
        
        if (e.target.id === 'btn-contraer-todo' || e.target.closest('#btn-contraer-todo')) {
            contraerTodo();
        }
    });
    
    // *** AGREGAR AL FINAL DEL DOMContentLoaded: ***
    // EXPONER FUNCIONES AL SCOPE GLOBAL para usar en onclick
    window.loadContent = loadContent;
    window.loadInicioData = loadInicioData;
    
    // CARGAR CONTENIDO INICIAL (con persistencia)
    const initialSection = getCurrentSection();
    loadContent(initialSection);
    
    // NUEVA FUNCIÓN: Cargar datos de estadística (con cuadro específico)
    function loadEstadisticaData() {
        // Verificar si hay cuadro_id en la URL
        const urlParams = new URLSearchParams(window.location.search);
        const cuadroId = urlParams.get('cuadro_id');
        
        console.log('loadEstadisticaData - cuadroId:', cuadroId);
        
        if (cuadroId) {
            console.log(`Cargando cuadro específico: ${cuadroId}`);
            loadCuadroEspecifico(cuadroId);
        } else {
            console.log('Sección estadística sin cuadro específico');
            // Mostrar mensaje por defecto
            const cuadroInfoContainer = document.getElementById('cuadro-info-container');
            if (cuadroInfoContainer) {
                cuadroInfoContainer.innerHTML = `
                    <div class="text-center py-5">
                        <i class="bi bi-info-circle text-muted" style="font-size: 3rem;"></i>
                        <h4 class="mt-3 text-muted">Selecciona un cuadro estadístico</h4>
                        <p class="text-muted">Para ver un cuadro estadístico específico, selecciona uno desde el catálogo.</p>
                        
                        <div class="mt-4">
                            <button type="button" class="btn btn-success" onclick="loadContent('catalogo')">
                                <i class="bi bi-journal-text me-1"></i>Ir al Catálogo
                            </button>
                        </div>
                    </div>
                `;
            }
        }
    }

    // NUEVA FUNCIÓN: Cargar cuadro específico
    function loadCuadroEspecifico(cuadroId) {
        const baseUrl = window.SIGEM_BASE_URL || 
                       (window.location.pathname.includes('/m_aux/') ? '/m_aux/public/sigem' : '/sigem');
        
        const cuadroInfoContainer = document.getElementById('cuadro-info-container');
        const infoCuadroByClick = document.getElementById('info_cuadro_by_click');
        
        if (!cuadroInfoContainer) {
            console.error('No se encontró el contenedor cuadro-info-container');
            return;
        }
        
        // Mostrar loading en el contenedor específico
        cuadroInfoContainer.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-success" role="status">
                    <span class="visually-hidden">Cargando cuadro...</span>
                </div>
                <h4 class="mt-3 text-muted">Cargando cuadro estadístico</h4>
                <p class="text-muted">ID: ${cuadroId}</p>
            </div>
        `;
        
        // Cargar datos del cuadro
        fetch(`${baseUrl}/cuadro-data/${cuadroId}`)
            .then(response => response.json())
            .then(data => {
                console.log('Datos del cuadro cargados:', data);
                
                if (data.success && data.cuadro) {
                    // Generar HTML con la información del cuadro
                    cuadroInfoContainer.innerHTML = generateCuadroInfoHtml(data.cuadro, data.tema_info, data.subtema_info);
                    
                    // OPCIONAL: También mostrar en info_cuadro_by_click si existe
                    if (infoCuadroByClick) {
                        infoCuadroByClick.innerHTML = `
                            <div class="alert alert-success">
                                <i class="bi bi-check-circle me-2"></i>
                                <strong>Cuadro cargado:</strong> ${data.cuadro.codigo_cuadro} - ${data.cuadro.cuadro_estadistico_titulo}
                            </div>
                        `;
                    }
                } else {
                    cuadroInfoContainer.innerHTML = `
                        <div class="alert alert-warning text-center">
                            <i class="bi bi-exclamation-triangle fs-1"></i>
                            <h4 class="mt-3">Cuadro no encontrado</h4>
                            <p>El cuadro con ID <strong>${cuadroId}</strong> no existe o no está disponible.</p>
                            <div class="mt-4">
                                <button type="button" class="btn btn-success" onclick="loadContent('catalogo')">
                                    <i class="bi bi-journal-text me-1"></i>Volver al Catálogo
                                </button>
                            </div>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error cargando cuadro:', error);
                cuadroInfoContainer.innerHTML = `
                    <div class="alert alert-danger text-center">
                        <i class="bi bi-exclamation-triangle fs-1"></i>
                        <h4 class="mt-3">Error al cargar cuadro</h4>
                        <p>No se pudo cargar el cuadro estadístico.</p>
                        <small class="text-muted">Error: ${error.message}</small>
                        <div class="mt-4">
                            <button type="button" class="btn btn-success" onclick="loadContent('catalogo')">
                                <i class="bi bi-journal-text me-1"></i>Volver al Catálogo
                            </button>
                        </div>
                    </div>
                `;
            });
    }

    // NUEVA FUNCIÓN: Generar HTML para mostrar información del cuadro
    function generateCuadroInfoHtml(cuadro, temaInfo, subtemaInfo) {
        return `
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <div class="row align-items-center">
                        <div class="col-10">
                            <h4 class="mb-0">
                                <i class="bi bi-table me-2"></i>
                                ${cuadro.codigo_cuadro || 'Sin código'}
                            </h4>
                        </div>
                        <div class="col-2 text-end">
                            <button type="button" class="btn btn-light btn-sm" onclick="loadContent('catalogo')" title="Volver al catálogo">
                                <i class="bi bi-arrow-left"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Título del cuadro -->
                    <h3 class="text-success mb-3">
                        ${cuadro.cuadro_estadistico_titulo || 'Sin título'}
                    </h3>
                    
                    ${cuadro.cuadro_estadistico_subtitulo ? 
                        `<h5 class="text-muted mb-4">${cuadro.cuadro_estadistico_subtitulo}</h5>` 
                        : ''
                    }
                    
                    <!-- Información del tema y subtema -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="info-section">
                                <h6 class="fw-bold text-primary">
                                    <i class="bi bi-folder-fill me-1"></i>Tema
                                </h6>
                                <p class="mb-0">
                                    ${temaInfo ? temaInfo.tema_titulo : 'No especificado'}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-section">
                                <h6 class="fw-bold text-info">
                                    <i class="bi bi-collection-fill me-1"></i>Subtema
                                </h6>
                                <p class="mb-0">
                                    ${subtemaInfo ? subtemaInfo.subtema_titulo : 'No especificado'}
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Información técnica -->
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <tbody>
                                        <tr>
                                            <td class="fw-bold bg-light" style="width: 200px;">
                                                <i class="bi bi-hash me-1"></i>ID del Cuadro
                                            </td>
                                            <td>${cuadro.cuadro_estadistico_id || 'N/A'}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold bg-light">
                                                <i class="bi bi-code me-1"></i>Código
                                            </td>
                                            <td><code class="text-primary">${cuadro.codigo_cuadro || 'N/A'}</code></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold bg-light">
                                                <i class="bi bi-calendar-event me-1"></i>Última actualización
                                            </td>
                                            <td>${cuadro.updated_at ? new Date(cuadro.updated_at).toLocaleDateString('es-ES') : 'No disponible'}</td>
                                        </tr>
                                        ${cuadro.descripcion ? 
                                            `<tr>
                                                <td class="fw-bold bg-light">
                                                    <i class="bi bi-card-text me-1"></i>Descripción
                                                </td>
                                                <td>${cuadro.descripcion}</td>
                                            </tr>` 
                                            : ''
                                        }
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Botones de acción -->
                    <div class="text-center mt-4">
                        <button type="button" class="btn btn-success me-2" onclick="loadContent('catalogo')">
                            <i class="bi bi-arrow-left me-1"></i>Volver al Catálogo
                        </button>
                        <button type="button" class="btn btn-outline-primary" onclick="window.print()">
                            <i class="bi bi-printer me-1"></i>Imprimir Información
                        </button>
                    </div>
                </div>
            </div>
        `;
    }

    // CORREGIR FUNCIÓN: loadCatalogoData (estaba confundida con cuadro)
    function loadCatalogoData() {
        const baseUrl = window.SIGEM_BASE_URL || 
                       (window.location.pathname.includes('/m_aux/') ? '/m_aux/public/sigem' : '/sigem');

        fetch(`${baseUrl}/catalogo`)
            .then(response => response.json())
            .then(data => {
                console.log('=== DATOS RAW DEL CATÁLOGO ===');
                console.log('Response completa:', data);
                console.log('Success:', data.success);
                console.log('Total temas:', data);
                console.log('Temas detalle:', data.temas_detalle);
                console.log('Cuadros estadísticos:', data.cuadros_estadisticos);
                console.log('=== FIN DATOS RAW ===');
                
                if (data.success) {
                    const indiceContainer = document.getElementById('indice-container');
                    const cuadrosContainer = document.getElementById('cuadros-container');
                    
                    if (indiceContainer && data.temas_detalle) {
                        indiceContainer.innerHTML = generateEstructuraIndice(data.temas_detalle);
                    }
                    
                    if (cuadrosContainer && data.cuadros_estadisticos) {
                        cuadrosContainer.innerHTML = generateListaCuadros(data.cuadros_estadisticos);
                    }
                    
                    // Sincronizar alturas después de cargar contenido
                    sincronizarAlturas();
                } else {
                    const indiceContainer = document.getElementById('indice-container');
                    const cuadrosContainer = document.getElementById('cuadros-container');
                    
                    if (indiceContainer) {
                        indiceContainer.innerHTML = `
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-circle"></i>
                                Error al cargar catálogo: ${data.message}
                            </div>
                        `;
                    }
                    
                    if (cuadrosContainer) {
                        cuadrosContainer.innerHTML = `
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-circle"></i>
                                Error al cargar cuadros estadísticos
                            </div>
                        `;
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const indiceContainer = document.getElementById('indice-container');
                const cuadrosContainer = document.getElementById('cuadros-container');
                
                if (indiceContainer) {
                    indiceContainer.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-circle"></i>
                            Error de conexión al cargar catálogo
                        </div>
                    `;
                }
                
                if (cuadrosContainer) {
                    cuadrosContainer.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-circle"></i>
                            Error de conexión al cargar cuadros
                        </div>
                    `;
                }
            });
    }

    // *** AL FINAL: EXPONER NUEVAS FUNCIONES AL SCOPE GLOBAL ***
    window.loadContent = loadContent;
    window.loadInicioData = loadInicioData;
    window.loadEstadisticaData = loadEstadisticaData; // NUEVA
    window.loadCuadroEspecifico = loadCuadroEspecifico; // NUEVA
});