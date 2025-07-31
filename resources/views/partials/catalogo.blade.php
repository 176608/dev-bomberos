<div class="card shadow-sm">
    <div class="card-body">
        <h2 class="text-success mb-4 text-center">
            <i class="bi bi-journal-text"></i> Catálogo de Cuadros Estadísticos
        </h2>

        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            <strong>Sistema de clasificación:</strong> Para su fácil localización, los diferentes cuadros que conforman el módulo estadístico del SIGEM se identifican mediante una clave conformada por el número de tema, identificador del subtema y el número de cuadro estadístico.
        </div>

        <div class="card bg-light">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="bi bi-lightbulb me-2"></i>Ejemplo de Clasificación
                </h5>
            </div>
            <div class="card-body text-center">
                <img src="imagenes/ejem.png" alt="Ejemplo clave estadística" class="img-fluid mb-3 rounded shadow-sm" style="max-width: 100%; height: auto;">
                <div class="alert alert-light">
                    <small>
                        El cuadro de "<strong>Población por Municipio</strong>" se encuentra dentro del Tema 3. Sociodemográfico en el subtema de <strong>Población</strong>.
                    </small>
                </div>
            </div>
        </div>

        <p class="text-center lead">Son 6 temas principales y a cada uno le corresponden diferentes subtemas en donde encontramos los cuadros estadísticos.</p>

        <!-- USAR ESTRUCTURA EXACTA QUE FUNCIONA EN SIGEM_ADMIN -->
        <div class="row mt-4">
            <div class="col-lg-4">
                <div class="card bg-light">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Estructura de Índice</h5>
                    </div>
                    <div class="card-body">
                        <div id="indice-container">
                            <div class="loading-state">
                                <div class="loading-spinner"></div>
                                <p>Cargando índice...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card bg-light">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-table me-2"></i>Cuadros Estadísticos
                            <span id="cuadros-count" class="badge bg-light text-dark ms-2">0 cuadros</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="cuadros-container">
                            <div class="loading-state">
                                <div class="loading-spinner"></div>
                                <p>Cargando cuadros...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* === ESTILOS COPIADOS DE SIGEM_ADMIN QUE FUNCIONAN === */
.loading-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 40px 20px;
    color: #6c757d;
}

.loading-spinner {
    width: 24px;
    height: 24px;
    border: 3px solid #f3f3f3;
    border-top: 3px solid #2a6e48;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-bottom: 15px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* === EFECTOS DE FOCUS COPIADOS DEL SIGEM_ADMIN === */
.highlight-focus {
    background-color: #fff3cd !important;
    border: 2px solid #ffc107 !important;
    box-shadow: 0 0 15px rgba(255, 193, 7, 0.5) !important;
    animation: pulseHighlight 1s ease-in-out;
}

@keyframes pulseHighlight {
    0% { 
        transform: scale(1); 
        box-shadow: 0 0 15px rgba(255, 193, 7, 0.5);
    }
    50% { 
        transform: scale(1.02); 
        box-shadow: 0 0 25px rgba(255, 193, 7, 0.8);
    }
    100% { 
        transform: scale(1); 
        box-shadow: 0 0 15px rgba(255, 193, 7, 0.5);
    }
}

/* === EFECTOS HOVER COPIADOS DEL SIGEM_ADMIN === */
.indice-tema-header:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2) !important;
}

.indice-subtema-row:hover {
    background-color: #e8f4f8 !important;
    transform: translateX(5px) !important;
}

/* === ALTURAS IGUALES COPIADAS DEL SIGEM_ADMIN === */
#indice-container, #cuadros-container {
    overflow-y: auto;
}

.catalogo-row {
    display: flex;
    align-items: stretch;
}

.catalogo-row .card {
    height: 100%;
}

.catalogo-row .card-body {
    display: flex;
    flex-direction: column;
    height: 100%;
}

/* === RESPONSIVE COPIADO DEL SIGEM_ADMIN === */
@media (max-width: 768px) {
    .catalogo-row .card-body > div {
        height: 400px;
    }
}

@media (max-width: 576px) {
    .catalogo-row .card-body > div {
        height: 300px;
    }
}
</style>

<script>
// Cargar datos del catálogo cuando la página se carga
document.addEventListener('DOMContentLoaded', function() {
    loadCatalogoData();
});

// Función para cargar datos de catálogo dinámicamente
function loadCatalogoData() {
    // Determinar la URL base basada en la ruta actual
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
                
                // Sincronizar alturas de los contenedores
                sincronizarAlturas();
            } else {
                console.error('Error en la respuesta del catálogo:', data.message);
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

// Función para generar HTML para el índice
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

// Función para generar HTML para los cuadros
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
                        <div class="px-3 py-2 border-bottom" style="${bgColor}">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <span class="badge rounded-pill bg-secondary">
                                        ${cuadro.codigo_cuadro || 'N/A'}
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <a href="#" onclick="verCuadro(${cuadro.cuadro_id || cuadro.id}, '${cuadro.codigo_cuadro || ''}'); return false;" 
                                       class="text-decoration-none" style="color: inherit;">
                                        <span class="fw-bold">${cuadro.titulo_cuadro || cuadro.titulo || 'Sin título'}</span>
                                    </a>
                                    <div class="small text-muted">
                                        ${cuadro.descripcion || 'Sin descripción'}
                                    </div>
                                </div>
                                <div>
                                    <button class="btn btn-sm btn-outline-primary" 
                                            onclick="verCuadro(${cuadro.cuadro_id || cuadro.id}, '${cuadro.codigo_cuadro || ''}'); return false;">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
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

// Función para organizar cuadros por tema y subtema
function organizarCuadrosPorTema(cuadrosEstadisticos) {
    const organizacion = {};

    cuadrosEstadisticos.forEach(cuadro => {
        // La información del tema viene a través del subtema
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
                subtema_info: subtemaInfo, // Guardar info completa del subtema
                cuadros: []
            };
        }

        // Agregar cuadro al subtema
        organizacion[temaKey].subtemas[subtemaKey].cuadros.push(cuadro);
    });

    // Ordenar por orden_indice
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

        // 3. Para cada subtema ordenado, ordenar sus cuadros
        subtemasOrdenados.forEach(subtemaKey => {
            const subtema = tema.subtemas[subtemaKey];
            
            organizacionOrdenada[temaKey].subtemas[subtemaKey] = {
                ...subtema,
                cuadros: subtema.cuadros.sort((a, b) => {
                    // Ordenar considerando orden_indice del subtema primero
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

// Función para comparar códigos de cuadro
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

// Funciones para interacción
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
    }
}

function verCuadro(cuadroId, codigo) {
    console.log(`Abriendo cuadro: ID=${cuadroId}, Código=${codigo}`);
    
    const baseUrl = window.SIGEM_BASE_URL || 
                   (window.location.pathname.includes('/m_aux/') ? '/m_aux/public/sigem' : '/sigem');
    const url = `${baseUrl}/estadistica/${cuadroId}`;
    
    window.open(url, '_blank');
}

// Función para sincronizar alturas
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
</script>