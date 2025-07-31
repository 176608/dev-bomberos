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
    const url = `${baseUrl}/estadistica/${cuadroId}`;
    
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
                        <div class="px-3 py-2 text-center fw-bold" style="min-width: 60px; border-right: 1px solid #ddd;">
                            ${subtema.clave_subtema || tema.clave_tema || 'N/A'}
                        </div>
                        <div class="px-3 py-2 flex-grow-1">
                            ${subtema.subtema_titulo}
                        </div>
                    </div>
                `;
            });
        } else {
            estructura += `
                <div class="px-3 py-2 text-muted">
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
                    ${subtema.clave || 'N/A'} ${subtema.nombre}
                </div>
            `;

            // Cuadros del subtema
            if (subtema.cuadros && subtema.cuadros.length > 0) {
                subtema.cuadros.forEach((cuadro, cuadroIndex) => {
                    const bgColor = cuadroIndex % 2 === 0 ? 'background-color: #f8f9fa;' : 'background-color: white;';

                    html += `
                        <div class="d-flex align-items-center border-bottom py-2 px-3" style="${bgColor}">
                            <div class="me-3" style="min-width: 80px;">
                                <code class="text-primary fw-bold">${cuadro.codigo_cuadro || 'N/A'}</code>
                            </div>
                            <div class="flex-grow-1 me-3" style="font-size: 12px;">
                                <div class="fw-bold">${cuadro.cuadro_estadistico_titulo || 'Sin título'}</div>
                                ${cuadro.cuadro_estadistico_subtitulo ? `<small class="text-muted">${cuadro.cuadro_estadistico_subtitulo}</small>` : ''}
                            </div>
                            <div>
                                <a href="#" class="btn btn-sm btn-outline-primary" 
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

    // FUNCIÓN: Cargar datos de catálogo (CORREGIDA para usar funciones de arriba)
    function loadCatalogoData() {
        const baseUrl = window.SIGEM_BASE_URL || 
                       (window.location.pathname.includes('/m_aux/') ? '/m_aux/public/sigem' : '/sigem');
        
        fetch(`${baseUrl}/catalogo`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const indiceContainer = document.getElementById('indice-container');
                    const cuadrosContainer = document.getElementById('cuadros-container');
                    const cuadrosCount = document.getElementById('cuadros-count');
                    
                    if (indiceContainer && data.temas_detalle) {
                        indiceContainer.innerHTML = generateEstructuraIndice(data.temas_detalle);
                    }
                    
                    if (cuadrosContainer && data.cuadros_estadisticos) {
                        cuadrosContainer.innerHTML = generateListaCuadros(data.cuadros_estadisticos);
                    }
                    
                    if (cuadrosCount) {
                        cuadrosCount.textContent = `${data.total_cuadros || 0} cuadros`;
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

    // FUNCIÓN: Generar estructura de índice (CORREGIR - remover líneas que causan error)
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
        
        return estructura;
    }

    // FUNCIÓN: Generar lista de cuadros (FALTABA ESTA FUNCIÓN)
    function generateListaCuadros(cuadrosEstadisticos) {
        if (!cuadrosEstadisticos || cuadrosEstadisticos.length === 0) {
            return `
                <div class="alert alert-warning text-center">
                    <i class="bi bi-exclamation-triangle fs-1"></i>
                    <h4 class="mt-3">No hay cuadros disponibles</h4>
                    <p class="mb-0">Actualmente no hay cuadros estadísticos configurados en el sistema.</p>
                </div>
            `;
        }

        let html = '';
        let currentTema = null;
        let currentSubtema = null;

        cuadrosEstadisticos.forEach((cuadro, index) => {
            // Verificar si es un nuevo tema
            if (currentTema !== cuadro.tema_numero) {
                // Cerrar subtema anterior si existe
                if (currentSubtema !== null) {
                    html += '</div>'; // Cerrar subtema-cuadros
                }
                
                // Cerrar tema anterior si existe
                if (currentTema !== null) {
                    html += '</div>'; // Cerrar tema-cuadros
                }

                // Nuevo tema
                currentTema = cuadro.tema_numero;
                currentSubtema = null;
                
                html += `
                    <div class="tema-cuadros" id="tema-cuadros-${currentTema}">
                        <div class="tema-cuadros-header">
                            <h3>
                                <span class="tema-numero">${currentTema}.</span>
                                <span class="tema-titulo">${cuadro.tema_titulo}</span>
                            </h3>
                        </div>
                `;
            }

            // Verificar si es un nuevo subtema
            if (currentSubtema !== cuadro.orden_indice) {
                // Cerrar subtema anterior si existe
                if (currentSubtema !== null) {
                    html += '</div>'; // Cerrar subtema-cuadros
                }

                // Nuevo subtema
                currentSubtema = cuadro.orden_indice;
                
                html += `
                    <div class="subtema-cuadros" id="subtema-cuadros-${currentTema}-${currentSubtema}">
                        <div class="subtema-cuadros-header">
                            <h4>
                                <span class="subtema-codigo">${cuadro.clave_subtema}</span>
                                <span class="subtema-titulo">${cuadro.subtema_titulo}</span>
                            </h4>
                        </div>
                        <div class="cuadros-grid">
                `;
            }

            // Agregar cuadro individual
            html += `
                <div class="cuadro-item" onclick="verCuadro(${cuadro.id}, '${cuadro.codigo}')">
                    <div class="cuadro-header">
                        <span class="cuadro-codigo">${cuadro.codigo}</span>
                        <i class="bi bi-box-arrow-up-right cuadro-icon"></i>
                    </div>
                    <div class="cuadro-body">
                        <h6 class="cuadro-titulo">${cuadro.titulo}</h6>
                        <p class="cuadro-descripcion">${cuadro.descripcion || 'Sin descripción disponible'}</p>
                    </div>
                    <div class="cuadro-footer">
                        <small class="text-muted">
                            <i class="bi bi-calendar3"></i>
                            ${cuadro.created_at ? new Date(cuadro.created_at).toLocaleDateString() : 'N/A'}
                        </small>
                    </div>
                </div>
            `;
        });

        // Cerrar containers abiertos
        if (currentSubtema !== null) {
            html += '</div></div>'; // Cerrar cuadros-grid y subtema-cuadros
        }
        if (currentTema !== null) {
            html += '</div>'; // Cerrar tema-cuadros
        }

        return html;
    }

    // FUNCIÓN: Sincronizar alturas (FALTABA ESTA FUNCIÓN)
    function sincronizarAlturas() {
        // Función para igualar alturas entre sidebar y content si es necesario
        setTimeout(() => {
            const sidebar = document.querySelector('.catalogo-sidebar');
            const mainContent = document.querySelector('.catalogo-main-content');
            
            if (sidebar && mainContent) {
                const sidebarHeight = sidebar.scrollHeight;
                const contentHeight = mainContent.scrollHeight;
                
                console.log(`Alturas sincronizadas - Sidebar: ${sidebarHeight}px, Content: ${contentHeight}px`);
            }
        }, 500);
    }

    // FUNCIÓN: Toggle de tema (CORREGIDA - no duplicada)
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

    // FUNCIÓN: Expandir todos los temas (CORREGIDA)
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

    // FUNCIÓN: Contraer todos los temas (CORREGIDA)
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
    
    // CARGAR CONTENIDO INICIAL (con persistencia)
    const initialSection = getCurrentSection();
    console.log(`Sección inicial: ${initialSection}`);
    loadContent(initialSection);
});