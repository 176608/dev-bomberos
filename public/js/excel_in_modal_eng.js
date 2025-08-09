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
                
                html += `<${tag} class="${classes}"${mergeAttrs}>${cellData.displayValue}</${tag}>`;
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
                    font-size: 11px;
                    border-collapse: collapse;
                    width: auto !important;
                    font-family: 'Segoe UI', Arial, sans-serif;
                    table-layout: fixed;
                }
                .excel-table th, .excel-table td {
                    border: 1px solid #c0c0c0;
                    padding: 2px 4px;
                    text-align: center;
                    vertical-align: middle;
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                    line-height: 1.2;
                }
                .excel-table .header-row {
                    background-color: #e7e6e6;
                    font-weight: bold;
                    color: #000;
                }
                .excel-table .footer-row {
                    background-color: #f5f5f5;
                    font-weight: bold;
                    border-top: 2px solid #333;
                }
                .excel-table .data-row {
                    background-color: #fff;
                }
                .excel-table .number-cell {
                    text-align: right;
                }
                .excel-table .text-cell {
                    text-align: left;
                }
                .excel-table .empty-cell {
                    background-color: #fafafa;
                }
                .excel-table .custom-border {
                    border: 2px solid #000;
                }
                .excel-viewer-container {
                    max-height: 75vh;
                    overflow-y: auto;
                }
                .excel-table-wrapper {
                    width: fit-content;
                    max-width: 100%;
                    overflow-x: auto;
                    margin: 0 auto;
                }
                /* Hover effect para mejor UX */
                .excel-table tbody tr:hover {
                    background-color: #f8f9fa;
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