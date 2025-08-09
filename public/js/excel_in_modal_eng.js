/**
 * Excel in Modal Engine - Motor para renderizado de archivos Excel en modales
 * Maneja la visualización de archivos Excel con formato preservado
 */
class ExcelModalEngine {
    constructor() {
        this.sheetJSLoaded = false;
        this.loadingPromise = null;
    }

    /**
     * Inicializar el motor - Precargar SheetJS si es necesario
     */
    async init() {
        if (typeof XLSX !== 'undefined') {
            this.sheetJSLoaded = true;
            return Promise.resolve();
        }

        if (this.loadingPromise) {
            return this.loadingPromise;
        }

        this.loadingPromise = new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js';
            script.onload = () => {
                this.sheetJSLoaded = true;
                console.log('SheetJS cargado correctamente');
                resolve();
            };
            script.onerror = () => {
                console.error('Error al cargar SheetJS');
                reject(new Error('No se pudo cargar la biblioteca SheetJS'));
            };
            document.head.appendChild(script);
        });

        return this.loadingPromise;
    }

    /**
     * Renderizar Excel en contenedor específico
     * @param {string} containerId - ID del contenedor donde renderizar
     * @param {string} excelUrl - URL del archivo Excel
     * @param {string} fileName - Nombre del archivo
     */
    async renderExcelInContainer(containerId, excelUrl, fileName) {
        const container = document.getElementById(containerId);
        if (!container) {
            throw new Error(`Contenedor ${containerId} no encontrado`);
        }

        // Mostrar loading inicial
        this.showLoadingState(container, 'Cargando biblioteca para visualizar Excel...');

        try {
            // Asegurar que SheetJS esté cargado
            await this.init();

            // Cambiar mensaje de loading
            this.showLoadingState(container, 'Procesando archivo Excel...');

            // Obtener y procesar el archivo
            const arrayBuffer = await this.fetchExcelFile(excelUrl);
            const htmlContent = await this.processExcelFile(arrayBuffer, fileName, excelUrl);
            
            // Renderizar resultado
            container.innerHTML = htmlContent;

        } catch (error) {
            console.error('Error al renderizar Excel:', error);
            this.showErrorState(container, error.message, excelUrl);
        }
    }

    /**
     * Obtener archivo Excel via fetch
     */
    async fetchExcelFile(excelUrl) {
        const response = await fetch(excelUrl);
        if (!response.ok) {
            throw new Error(`Error al cargar el archivo Excel (${response.status})`);
        }
        return await response.arrayBuffer();
    }

    /**
     * Procesar archivo Excel y generar HTML
     */
    async processExcelFile(arrayBuffer, fileName, excelUrl) {
        const data = new Uint8Array(arrayBuffer);
        const workbook = XLSX.read(data, { type: 'array', cellStyles: true });
        
        // Obtener la primera hoja
        const firstSheetName = workbook.SheetNames[0];
        const worksheet = workbook.Sheets[firstSheetName];
        
        // Crear tabla HTML personalizada que respete el formato
        const tablaHTML = this.createFormattedTable(worksheet, workbook);
        
        return this.wrapInContainer(tablaHTML, fileName, excelUrl);
    }

    /**
     * Crear tabla HTML con formato preservado y optimizada
     */
    createFormattedTable(worksheet, workbook) {
        const range = XLSX.utils.decode_range(worksheet['!ref'] || 'A1:A1');
        const mergedCells = worksheet['!merges'] || [];
        
        // 1. PRIMERO: Determinar el rango útil (eliminar filas/columnas completamente vacías)
        const usefulRange = this.calculateUsefulRange(worksheet, range);
        console.log('Rango útil calculado:', usefulRange);
        
        // 2. SEGUNDO: Calcular anchos de columnas basado en contenido
        const columnWidths = this.calculateColumnWidths(worksheet, usefulRange, workbook);
        console.log('Anchos de columnas calculados:', columnWidths);
        
        // Mapear celdas combinadas
        const mergedCellsMap = this.createMergedCellsMap(mergedCells);
        
        let html = '<table class="table excel-table table-bordered">';
        let dynamicStyles = [];
        
        // Agregar estilos de ancho de columnas
        html += '<colgroup>';
        for (let c = usefulRange.s.c; c <= usefulRange.e.c; c++) {
            const width = columnWidths[c] || 100;
            html += `<col style="width: ${width}px; min-width: ${width}px;">`;
        }
        html += '</colgroup>';
        
        // Generar filas usando el rango útil
        for (let r = usefulRange.s.r; r <= usefulRange.e.r; r++) {
            const rowClasses = this.getRowClasses(r, usefulRange.e.r);
            html += `<tr class="${rowClasses}">`;
            
            for (let c = usefulRange.s.c; c <= usefulRange.e.c; c++) {
                const cellKey = `${r}_${c}`;
                const mergeInfo = mergedCellsMap[cellKey];
                
                // Saltar celdas que están combinadas (excepto la master)
                if (mergeInfo && !mergeInfo.isMaster) {
                    continue;
                }
                
                const cellRef = XLSX.utils.encode_cell({ r, c });
                const cell = worksheet[cellRef];
                
                // Procesar contenido y formato de la celda
                const cellData = this.processCellData(cell, cellRef, workbook);
                
                // Generar atributos de combinación
                const mergeAttrs = this.getMergeAttributes(mergeInfo);
                
                // Generar estilos dinámicos si hay formato específico
                const styleId = `cell-${r}-${c}`;
                if (cellData.hasCustomStyle) {
                    dynamicStyles.push(`.${styleId} { ${cellData.customStyle} }`);
                }
                
                const tag = (r <= 1) ? 'th' : 'td';
                const classes = [
                    ...cellData.classes,
                    cellData.hasCustomStyle ? styleId : ''
                ].filter(Boolean).join(' ');
                
                html += `<${tag} class="${classes}" style="${cellData.customStyle}"${mergeAttrs}>${cellData.displayValue}</${tag}>`;
            }
            
            html += '</tr>';
        }
        
        html += '</table>';
        
        // Agregar estilos dinámicos si los hay
        if (dynamicStyles.length > 0) {
            html = `<style>${dynamicStyles.join('\n')}</style>${html}`;
        }
        
        return html;
    }

    /**
     * Calcular el rango útil eliminando filas y columnas completamente vacías
     */
    calculateUsefulRange(worksheet, originalRange) {
        let minRow = originalRange.e.r;
        let maxRow = originalRange.s.r;
        let minCol = originalRange.e.c;
        let maxCol = originalRange.s.c;
        
        // Recorrer todas las celdas para encontrar las que tienen contenido
        for (let r = originalRange.s.r; r <= originalRange.e.r; r++) {
            for (let c = originalRange.s.c; c <= originalRange.e.c; c++) {
                const cellRef = XLSX.utils.encode_cell({ r, c });
                const cell = worksheet[cellRef];
                
                // Si la celda tiene contenido (no está vacía)
                if (cell && cell.v !== undefined && cell.v !== null && cell.v !== '') {
                    minRow = Math.min(minRow, r);
                    maxRow = Math.max(maxRow, r);
                    minCol = Math.min(minCol, c);
                    maxCol = Math.max(maxCol, c);
                }
            }
        }
        
        // Si no se encontró contenido, usar el rango original
        if (minRow > maxRow || minCol > maxCol) {
            return originalRange;
        }
        
        return {
            s: { r: minRow, c: minCol },
            e: { r: maxRow, c: maxCol }
        };
    }

    /**
     * Calcular anchos de columnas basado en el contenido
     */
    calculateColumnWidths(worksheet, range, workbook) {
        const columnWidths = {};
        const minWidth = 50;  // Ancho mínimo
        const maxWidth = 300; // Ancho máximo
        const paddingFactor = 8; // Factor para calcular pixels por caracter
        
        for (let c = range.s.c; c <= range.e.c; c++) {
            let maxContentLength = 0;
            
            // Revisar todas las celdas de esta columna
            for (let r = range.s.r; r <= range.e.r; r++) {
                const cellRef = XLSX.utils.encode_cell({ r, c });
                const cell = worksheet[cellRef];
                
                if (cell && cell.v !== undefined && cell.v !== null) {
                    // Obtener el valor formateado para medir su longitud
                    const displayValue = this.formatCellValue(cell);
                    const contentLength = displayValue.toString().length;
                    maxContentLength = Math.max(maxContentLength, contentLength);
                }
            }
            
            // Calcular ancho en pixels
            let calculatedWidth = maxContentLength * paddingFactor + 20; // +20 para padding
            
            // Aplicar límites mínimo y máximo
            calculatedWidth = Math.max(minWidth, Math.min(maxWidth, calculatedWidth));
            
            columnWidths[c] = calculatedWidth;
        }
        
        return columnWidths;
    }

    /**
     * Procesar datos y formato de una celda individual (MEJORADO)
     */
    processCellData(cell, cellRef, workbook) {
        let displayValue = '';
        let classes = [];
        let customStyle = '';
        let hasCustomStyle = false;
        
        // Si no hay celda o está vacía, retornar celda vacía
        if (!cell || cell.v === undefined || cell.v === null || cell.v === '') {
            return { displayValue: '&nbsp;', classes: ['empty-cell'], customStyle, hasCustomStyle };
        }
        
        // Procesar valor de la celda
        displayValue = this.formatCellValue(cell);
        
        // Procesar estilos si están disponibles
        if (cell.s) {
            const styleData = this.extractCellStyles(cell.s);
            customStyle = styleData.cssString;
            hasCustomStyle = styleData.hasStyles;
            classes.push(...styleData.classes);
        }
        
        // Clases por tipo de dato
        if (cell.t === 'n') {
            classes.push('number-cell');
        } else {
            classes.push('text-cell');
        }
        
        return { displayValue, classes, customStyle, hasCustomStyle };
    }

    /**
     * Formatear valor de celda según su tipo y formato
     */
    formatCellValue(cell) {
        if (cell.v === undefined || cell.v === null) {
            return '';
        }
        
        if (cell.t === 'n' && cell.v !== undefined) {
            // Número - formatear según el formato definido
            if (cell.z) {
                if (cell.z.includes('%')) {
                    return (cell.v * 100).toFixed(2) + '%';
                } else if (cell.z.includes('$')) {
                    return '$' + cell.v.toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                } else if (cell.z.includes(',')) {
                    return cell.v.toLocaleString();
                }
            }
            return cell.v.toString();
        }
        
        return cell.v.toString();
    }

    /**
     * Extraer estilos CSS de una celda Excel
     */
    extractCellStyles(cellStyle) {
        let cssArray = [];
        let classes = [];
        
        // Fuente
        if (cellStyle.font) {
            if (cellStyle.font.bold) {
                cssArray.push('font-weight: bold');
            }
            if (cellStyle.font.italic) {
                cssArray.push('font-style: italic');
            }
            if (cellStyle.font.underline) {
                cssArray.push('text-decoration: underline');
            }
            if (cellStyle.font.sz) {
                cssArray.push(`font-size: ${cellStyle.font.sz}px`);
            }
            if (cellStyle.font.color && cellStyle.font.color.rgb) {
                cssArray.push(`color: #${cellStyle.font.color.rgb}`);
            }
        }
        
        // Alineación
        if (cellStyle.alignment) {
            if (cellStyle.alignment.horizontal) {
                cssArray.push(`text-align: ${cellStyle.alignment.horizontal}`);
            }
            if (cellStyle.alignment.vertical) {
                cssArray.push(`vertical-align: ${cellStyle.alignment.vertical}`);
            }
        }
        
        // Fondo
        if (cellStyle.fill && cellStyle.fill.fgColor && cellStyle.fill.fgColor.rgb) {
            cssArray.push(`background-color: #${cellStyle.fill.fgColor.rgb}`);
        }
        
        // Bordes
        if (cellStyle.border) {
            if (cellStyle.border.top && cellStyle.border.top.style) {
                const borderColor = cellStyle.border.top.color && cellStyle.border.top.color.rgb ? 
                    `#${cellStyle.border.top.color.rgb}` : '#000000';
                cssArray.push(`border-top: 1px solid ${borderColor}`);
            }
            if (cellStyle.border.right && cellStyle.border.right.style) {
                const borderColor = cellStyle.border.right.color && cellStyle.border.right.color.rgb ? 
                    `#${cellStyle.border.right.color.rgb}` : '#000000';
                cssArray.push(`border-right: 1px solid ${borderColor}`);
            }
            if (cellStyle.border.bottom && cellStyle.border.bottom.style) {
                const borderColor = cellStyle.border.bottom.color && cellStyle.border.bottom.color.rgb ? 
                    `#${cellStyle.border.bottom.color.rgb}` : '#000000';
                cssArray.push(`border-bottom: 1px solid ${borderColor}`);
            }
            if (cellStyle.border.left && cellStyle.border.left.style) {
                const borderColor = cellStyle.border.left.color && cellStyle.border.left.color.rgb ? 
                    `#${cellStyle.border.left.color.rgb}` : '#000000';
                cssArray.push(`border-left: 1px solid ${borderColor}`);
            }
        }
        
        return {
            cssString: cssArray.join('; '),
            classes: classes,
            hasStyles: cssArray.length > 0
        };
    }

    /**
     * Crear mapa de celdas combinadas
     */
    createMergedCellsMap(mergedCells) {
        const map = {};
        mergedCells.forEach(merge => {
            for (let r = merge.s.r; r <= merge.e.r; r++) {
                for (let c = merge.s.c; c <= merge.e.c; c++) {
                    map[`${r}_${c}`] = {
                        isMaster: r === merge.s.r && c === merge.s.c,
                        rowspan: merge.e.r - merge.s.r + 1,
                        colspan: merge.e.c - merge.s.c + 1,
                        merge: merge
                    };
                }
            }
        });
        return map;
    }

    /**
     * Obtener clases CSS para una fila (MEJORADO)
     */
    getRowClasses(rowIndex, lastRowIndex) {
        // Solo la primera fila es header
        if (rowIndex === 0) return 'header-row';
        // Solo la última fila es footer si tiene datos especiales
        if (rowIndex === lastRowIndex) return 'footer-row';
        return 'data-row';
    }

    /**
     * Generar atributos HTML para celdas combinadas
     */
    getMergeAttributes(mergeInfo) {
        if (!mergeInfo || !mergeInfo.isMaster) return '';
        
        let attrs = '';
        if (mergeInfo.rowspan > 1) {
            attrs += ` rowspan="${mergeInfo.rowspan}"`;
        }
        if (mergeInfo.colspan > 1) {
            attrs += ` colspan="${mergeInfo.colspan}"`;
        }
        return attrs;
    }

    /**
     * Envolver tabla en contenedor con estilos
     */
    wrapInContainer(tableHTML, fileName, excelUrl) {
        return `
            <div class="excel-viewer-container">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">
                        <i class="bi bi-file-earmark-excel me-2"></i>
                        ${fileName || 'Archivo Excel'}
                    </h5>
                    <a href="${excelUrl}" class="btn btn-sm btn-outline-success" download>
                        <i class="bi bi-download me-1"></i>Descargar
                    </a>
                </div>
                <div class="table-responsive excel-table-wrapper">
                    ${this.getTableStyles()}
                    ${tableHTML}
                </div>
            </div>
        `;
    }

    /**
     * Obtener estilos CSS para las tablas (MEJORADO Y COMPACTO)
     */
    getTableStyles() {
        return `
            <style>
                .excel-table {
                    font-family: 'Calibri', 'Segoe UI', Arial, sans-serif;
                    font-size: 12px;
                    border-collapse: collapse;
                    width: auto !important;
                    box-shadow: 0 2px 4px rgba(20, 19, 19, 0.2);
                    border-radius: 8px;
                    overflow: hidden;
                }
                .excel-table th, .excel-table td {
                    padding: 8px 12px;
                    text-align: center;
                    vertical-align: middle;
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                    border: 1px solid #ac9d9dff;
                    transition: background-color 0.3s ease, border-color 0.3s ease;
                }
                .excel-table th {
                    background-color: #eeb2b2ff;
                    font-weight: bold;
                    color: #333;
                    border-top-left-radius: 8px;
                    border-top-right-radius: 8px;
                }
                .excel-table td {
                    background-color: #e7a0a0ff;
                }
                /* Efecto hover */
                .excel-table tbody tr:hover {
                    background-color: #83b0ddff;
                    border-color: #464040ff;
                }
                .excel-table tbody tr:hover td {
                    background-color: #a85c5cff;
                }
                /* Diferenciar tipos de datos */
                .excel-table .text-cell {
                    background-color: #7cd4b3ff;
                }
                .excel-table .number-cell {
                    background-color: #74bb86ff;
                    text-align: right;
                }
                .excel-table .percentage-cell {
                    background-color: #be8686ff;
                    text-align: right;
                }
                .excel-table .header-row {
                    background-color: #5a8f9cff;
                }
                .excel-table .footer-row {
                    background-color: #456e97ff;
                }
                .excel-table .merged-cell {
                    background-color: #846688ff;
                }
                .excel-viewer-container {
                    max-height: 75vh;
                    overflow-y: auto;
                    padding: 10px;
                    border: 1px solid #ccc;
                    border-radius: 8px;
                    box-shadow: 0 2px 4px rgba(20, 19, 19, 0.16);
                }
            </style>
        `;
    }

    /**
     * Mostrar estado de carga
     */
    showLoadingState(container, message) {
        container.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-success" role="status"></div>
                <p class="mt-3">${message}</p>
            </div>
        `;
    }

    /**
     * Mostrar estado de error
     */
    showErrorState(container, errorMessage, excelUrl) {
        container.innerHTML = `
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle me-2"></i>
                ${errorMessage}
            </div>
            <div class="text-center mt-3">
                <a href="${excelUrl}" class="btn btn-primary" download>
                    <i class="bi bi-download me-2"></i>Intentar descargar directamente
                </a>
            </div>
        `;
    }
}



// Instancia global
window.ExcelModalEngine = new ExcelModalEngine();

// Función para mostrar modal con Excel (compatibilidad con código existente)
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
                ExcelModalEngine.renderExcelInContainer(`excel-container-${modalId}`, excelUrl, data.nombre_archivo);
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