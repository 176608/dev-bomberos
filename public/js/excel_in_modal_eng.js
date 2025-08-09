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
     * Crear tabla HTML con formato preservado
     */
    createFormattedTable(worksheet, workbook) {
        const range = XLSX.utils.decode_range(worksheet['!ref'] || 'A1:A1');
        const mergedCells = worksheet['!merges'] || [];
        
        // Mapear celdas combinadas
        const mergedCellsMap = this.createMergedCellsMap(mergedCells);
        
        let html = '<table class="table excel-table table-bordered">';
        let dynamicStyles = [];
        
        for (let r = range.s.r; r <= range.e.r; r++) {
            const rowClasses = this.getRowClasses(r, range.e.r);
            html += `<tr class="${rowClasses}">`;
            
            for (let c = range.s.c; c <= range.e.c; c++) {
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
     * Procesar datos y formato de una celda individual
     */
    processCellData(cell, cellRef, workbook) {
        let displayValue = '';
        let classes = [];
        let customStyle = '';
        let hasCustomStyle = false;
        
        if (!cell) {
            return { displayValue, classes, customStyle, hasCustomStyle };
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
            classes.push('center-cell');
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
        
        // Bordes (simplificado)
        if (cellStyle.border) {
            classes.push('custom-border');
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
     * Obtener clases CSS para una fila
     */
    getRowClasses(rowIndex, lastRowIndex) {
        if (rowIndex <= 1) return 'header-row';
        if (rowIndex === lastRowIndex) return 'footer-row';
        return '';
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
     * Obtener estilos CSS para las tablas
     */
    getTableStyles() {
        return `
            <style>
                .excel-table {
                    font-size: 12px;
                    border-collapse: collapse;
                    width: 100%;
                    font-family: Arial, sans-serif;
                }
                .excel-table th, .excel-table td {
                    border: 1px solid #ddd;
                    padding: 4px 8px;
                    text-align: center;
                    vertical-align: middle;
                    white-space: nowrap;
                }
                .excel-table .header-row {
                    background-color: #f8f9fa;
                    font-weight: bold;
                }
                .excel-table .footer-row {
                    background-color: #f1f3f4;
                    font-weight: bold;
                    border-top: 2px solid #495057;
                }
                .excel-table .number-cell {
                    text-align: right;
                }
                .excel-table .center-cell {
                    text-align: center;
                }
                .excel-table .custom-border {
                    border: 2px solid #000;
                }
                .excel-viewer-container {
                    max-height: 70vh;
                    overflow-y: auto;
                }
                .excel-table-wrapper {
                    max-width: 100%;
                    overflow-x: auto;
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