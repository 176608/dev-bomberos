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
                
                // NUEVA LÓGICA: Si es una celda combinada, agregar clase especial y centrado
                if (mergeInfo && mergeInfo.isMaster) {
                    cellData.classes.push('merged-cell');
                    
                    // Verificar si es un row completo combinado
                    const totalColumns = usefulRange.e.c - usefulRange.s.c + 1;
                    if (mergeInfo.colspan === totalColumns) {
                        cellData.classes.push('full-row-merged');
                    }
                    
                    // Forzar centrado para todas las celdas combinadas
                    if (!cellData.customStyle.includes('text-align')) {
                        cellData.customStyle += cellData.customStyle ? '; text-align: center' : 'text-align: center';
                        cellData.hasCustomStyle = true;
                    }
                }
                
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
     * Extraer estilos CSS de una celda Excel con soporte completo de bordes
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
            if (cellStyle.font.name) {
                cssArray.push(`font-family: '${cellStyle.font.name}', 'Calibri', sans-serif`);
            }
        }
        
        // Alineación
        if (cellStyle.alignment) {
            if (cellStyle.alignment.horizontal) {
                const alignmentMap = {
                    'left': 'left',
                    'center': 'center', 
                    'right': 'right',
                    'justify': 'justify',
                    'centerContinuous': 'center',
                    'distributed': 'justify'
                };
                const align = alignmentMap[cellStyle.alignment.horizontal] || cellStyle.alignment.horizontal;
                cssArray.push(`text-align: ${align}`);
            }
            if (cellStyle.alignment.vertical) {
                const verticalMap = {
                    'top': 'top',
                    'middle': 'middle',
                    'bottom': 'bottom',
                    'justify': 'baseline',
                    'distributed': 'middle'
                };
                const vAlign = verticalMap[cellStyle.alignment.vertical] || cellStyle.alignment.vertical;
                cssArray.push(`vertical-align: ${vAlign}`);
            }
            if (cellStyle.alignment.wrapText) {
                cssArray.push('white-space: pre-wrap');
                cssArray.push('word-wrap: break-word');
            }
        }
        
        // Fondo
        if (cellStyle.fill && cellStyle.fill.fgColor && cellStyle.fill.fgColor.rgb) {
            cssArray.push(`background-color: #${cellStyle.fill.fgColor.rgb}`);
        }
        
        // BORDES COMPLETOS Y MEJORADOS - Esta es la parte clave
        if (cellStyle.border) {
            // Mapeo completo de estilos de borde de Excel a CSS
            const borderStyleMap = {
                // Estilos básicos
                'thin': '1px solid',
                'medium': '2px solid', 
                'thick': '3px solid',
                
                // Estilos punteados y discontinuos
                'dotted': '1px dotted',
                'dashed': '2px dashed',
                'hair': '0.5px solid', // Muy fino
                
                // Estilos combinados con grosor
                'mediumDashed': '2px dashed',
                'thickDashed': '3px dashed',
                'mediumDotted': '2px dotted',
                
                // Estilos de puntos y rayas
                'dashDot': '1px dashed', 
                'mediumDashDot': '2px dashed',
                'dashDotDot': '1px dotted',
                'mediumDashDotDot': '2px dotted',
                'slantDashDot': '2px dashed',
                
                // Estilos especiales
                'double': '3px double',
                'none': 'none',
                
                // Fallback para estilos no reconocidos
                'default': '1px solid'
            };
            
            // Función auxiliar para obtener el estilo completo del borde
            const getBorderStyle = (border) => {
                if (!border || !border.style) return null;
                
                const style = borderStyleMap[border.style] || borderStyleMap['default'];
                let color = '#000000'; // Color por defecto
                
                // Obtener color del borde
                if (border.color) {
                    if (border.color.rgb) {
                        color = `#${border.color.rgb}`;
                    } else if (border.color.indexed !== undefined) {
                        // Mapear colores indexados comunes de Excel
                        const indexedColors = {
                            0: '#000000', // Negro
                            1: '#FFFFFF', // Blanco
                            2: '#FF0000', // Rojo
                            3: '#00FF00', // Verde
                            4: '#0000FF', // Azul
                            5: '#FFFF00', // Amarillo
                            6: '#FF00FF', // Magenta
                            7: '#00FFFF', // Cian
                            8: '#800000', // Marrón
                            9: '#008000', // Verde oscuro
                            10: '#000080', // Azul marino
                            // Agregar más según sea necesario
                        };
                        color = indexedColors[border.color.indexed] || '#000000';
                    } else if (border.color.theme !== undefined) {
                        // Colores de tema de Excel (simplificado)
                        const themeColors = {
                            0: '#FFFFFF', // Fondo 1
                            1: '#000000', // Texto 1
                            2: '#E7E6E6', // Fondo 2
                            3: '#44546A', // Texto 2
                            4: '#5B9BD5', // Acento 1
                            5: '#70AD47', // Acento 2
                            6: '#FFC000', // Acento 3
                            7: '#F79646', // Acento 4
                            8: '#9F4F96', // Acento 5
                            9: '#4BACC6', // Acento 6
                        };
                        color = themeColors[border.color.theme] || '#000000';
                    }
                }
                
                return `${style} ${color}`;
            };
            
            // Aplicar bordes individuales con detección completa
            const borderSides = ['top', 'right', 'bottom', 'left'];
            let hasBorders = false;
            
            borderSides.forEach(side => {
                if (cellStyle.border[side]) {
                    const borderStyle = getBorderStyle(cellStyle.border[side]);
                    if (borderStyle && borderStyle !== 'none') {
                        cssArray.push(`border-${side}: ${borderStyle}`);
                        hasBorders = true;
                    }
                }
            });
            
            // Bordes diagonales (crear clases especiales para estos)
            if (cellStyle.border.diagonal) {
                const diagonalStyle = getBorderStyle(cellStyle.border.diagonal);
                if (diagonalStyle && diagonalStyle !== 'none') {
                    classes.push('diagonal-border');
                    // Crear estilo dinámico para diagonal
                    cssArray.push(`position: relative`);
                }
            }
            
            // Si tiene bordes, agregar clase para mejor control
            if (hasBorders) {
                classes.push('custom-borders');
            }
        }
        
        // Número de formato personalizado (para detectar formatos especiales)
        if (cellStyle.numFmt) {
            // Si es un formato de número personalizado, podríamos agregar clases específicas
            if (cellStyle.numFmt.includes('%')) {
                classes.push('percentage-format');
            } else if (cellStyle.numFmt.includes('$') || cellStyle.numFmt.includes('€')) {
                classes.push('currency-format');
            } else if (cellStyle.numFmt.includes('date') || cellStyle.numFmt.includes('yyyy')) {
                classes.push('date-format');
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
     * Obtener estilos CSS para las tablas - ACTUALIZADO CON SOPORTE COMPLETO DE BORDES
     */
    getTableStyles() {
        return `
            <style>
                .excel-table {
                    font-family: 'Calibri', 'Segoe UI', Arial, sans-serif;
                    font-size: 11px;
                    border-collapse: separate;
                    border-spacing: 0;
                    width: auto !important;
                    background-color: #ffffff;
                    border: 1px solid #d0d0d0;
                }
                
                .excel-table th, .excel-table td {
                    padding: 4px 8px;
                    text-align: center;
                    vertical-align: middle;
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                    border: 1px solid #d0d0d0;
                    line-height: 1.2;
                    position: relative;
                }
                
                /* Estilos para celdas con bordes personalizados */
                .excel-table .custom-borders {
                    /* Los estilos de borde se aplicarán inline desde extractCellStyles */
                }
                
                /* Soporte para bordes diagonales */
                .excel-table .diagonal-border::before {
                    content: '';
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    border-top: 1px solid #000;
                    transform-origin: top left;
                    transform: skewY(45deg);
                    pointer-events: none;
                }
                
                /* Estilos para diferentes tipos de formato */
                .excel-table .percentage-format {
                    text-align: right;
                    color: #0066cc;
                }
                
                .excel-table .currency-format {
                    text-align: right;
                    color: #006600;
                }
                
                .excel-table .date-format {
                    text-align: center;
                    color: #333;
                }
                
                /* Estilos para headers principales (títulos) - Verde más oscuro */
                .excel-table .header-row th {
                    background-color: #70ad47;
                    color: #ffffff;
                    font-weight: bold;
                    text-align: center;
                }
                
                /* Estilos para sub-headers - Verde medio */
                .excel-table .header-row:nth-child(2) th,
                .excel-table tr:nth-child(2) th {
                    background-color: #92c5f7;
                    color: #000000;
                    font-weight: bold;
                    text-align: center;
                }
                
                /* Celdas de datos regulares - Verde muy claro */
                .excel-table td {
                    background-color: #e2efda;
                    color: #000000;
                }
                
                /* Celdas de categorías/conceptos (primera columna) */
                .excel-table td:first-child:not(.merged-cell) {
                    background-color: #c6e0b4;
                    font-weight: bold;
                    text-align: left;
                    padding-left: 12px;
                }
                
                /* IMPORTANTE: Sobrescribir primera columna si es merged cell */
                .excel-table td:first-child.merged-cell {
                    text-align: center !important;
                    padding-left: 8px;
                }
                
                /* Números - alineación a la derecha */
                .excel-table .number-cell:not(.merged-cell) {
                    text-align: right;
                    background-color: #e2efda;
                }
                
                /* IMPORTANTE: Sobrescribir números si es merged cell */
                .excel-table .number-cell.merged-cell {
                    text-align: center !important;
                }
                
                /* Celdas de texto - alineación centrada */
                .excel-table .text-cell {
                    text-align: center;
                    background-color: #e2efda;
                }
                
                /* ESTILOS ESPECÍFICOS PARA CELDAS COMBINADAS */
                .excel-table .merged-cell {
                    background-color: inherit;
                    font-weight: bold;
                    text-align: center !important;
                    vertical-align: middle;
                }
                
                /* Estilos para filas completas combinadas (títulos principales) */
                .excel-table .full-row-merged {
                    background-color: #70ad47 !important;
                    color: #ffffff !important;
                    font-weight: bold;
                    text-align: center !important;
                    font-size: 12px;
                    padding: 8px !important;
                }
                
                /* Filas alternas para mejor legibilidad */
                .excel-table tbody tr:nth-child(even) td:not(.merged-cell):not(.full-row-merged) {
                    background-color: #f2f8ec;
                }
                
                .excel-table tbody tr:nth-child(even) td:first-child:not(.merged-cell) {
                    background-color: #d4e6c7;
                }
                
                /* Hover effect sutil */
                .excel-table tbody tr:hover td:not(.merged-cell):not(.full-row-merged) {
                    background-color: #d5e8d4 !important;
                    transition: background-color 0.2s ease;
                }
                
                /* Contenedor */
                .excel-viewer-container {
                    max-height: 80vh;
                    overflow-y: auto;
                    padding: 15px;
                    background-color: #ffffff;
                    border: 1px solid #d4d4d4;
                    border-radius: 4px;
                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                }
                
                .excel-table-wrapper {
                    width: fit-content;
                    max-width: 100%;
                    overflow-x: auto;
                    margin: 0 auto;
                    background-color: #ffffff;
                }
                
                /* Ajustes para celdas vacías */
                .excel-table .empty-cell {
                    background-color: #f8f9fa;
                }
                
                /* Título principal de la tabla si existe */
                .excel-table .main-title {
                    background-color: #70ad47;
                    color: #ffffff;
                    font-weight: bold;
                    font-size: 12px;
                    text-align: center;
                    padding: 8px;
                }
                
                /* Paleta de colores extendida para diferentes tipos de datos */
                .excel-table .positive-number {
                    color: #006600;
                    background-color: #e6f7e6;
                }
                
                .excel-table .negative-number {
                    color: #cc0000;
                    background-color: #ffe6e6;
                }
                
                .excel-table .zero-value {
                    color: #666666;
                    background-color: #f0f0f0;
                }
                
                .excel-table .percentage-high {
                    background-color: #e6f3ff;
                }
                
                .excel-table .percentage-medium {
                    background-color: #fff2e6;
                }
                
                .excel-table .percentage-low {
                    background-color: #f2f2f2;
                }
                
                /* Estilos para celdas de totales */
                .excel-table .total-cell {
                    background-color: #4472c4 !important;
                    color: #ffffff !important;
                    font-weight: bold;
                }
                
                /* Estilos para celdas de subtotales */
                .excel-table .subtotal-cell {
                    background-color: #70ad47 !important;
                    color: #ffffff !important;
                    font-weight: bold;
                }
                
                /* Estilos para celdas de notas o comentarios */
                .excel-table .note-cell {
                    background-color: #ffffcc;
                    font-style: italic;
                    color: #666600;
                }
                
                /* Estilos para celdas de advertencia */
                .excel-table .warning-cell {
                    background-color: #fff2cc;
                    color: #b35c00;
                }
                
                /* Estilos para celdas de error */
                .excel-table .error-cell {
                    background-color: #f8cbad;
                    color: #cc0000;
                }
                
                /* Estilos para celdas de éxito */
                .excel-table .success-cell {
                    background-color: #c6e0b4;
                    color: #006600;
                }
                
                /* Estilos para celdas de información */
                .excel-table .info-cell {
                    background-color: #bdd7ee;
                    color: #003366;
                }
                
                /* Estilos para celdas de fechas */
                .excel-table .date-cell {
                    background-color: #e2efd9;
                    color: #333333;
                }
                
                /* Estilos para celdas de tiempo */
                .excel-table .time-cell {
                    background-color: #dbe5f1;
                    color: #333333;
                }
                
                /* Estilos para celdas de moneda */
                .excel-table .currency-cell {
                    background-color: #e2efda;
                    color: #006600;
                }
                
                /* Estilos para celdas de porcentaje */
                .excel-table .percentage-cell {
                    background-color: #fff2cc;
                    color: #b35c00;
                }
                
                /* Estilos para celdas de texto largo */
                .excel-table .long-text-cell {
                    white-space: normal;
                    word-wrap: break-word;
                    max-width: 200px;
                }
                
                /* Estilos para celdas de texto corto */
                .excel-table .short-text-cell {
                    white-space: nowrap;
                }
                
                /* Estilos para celdas de encabezado secundario */
                .excel-table .sub-header-cell {
                    background-color: #a9d18e;
                    color: #000000;
                    font-weight: bold;
                }
                
                /* Estilos para celdas de pie de tabla */
                .excel-table .footer-cell {
                    background-color: #4472c4;
                    color: #ffffff;
                    font-weight: bold;
                }
                
                /* Estilos para celdas de resumen */
                .excel-table .summary-cell {
                    background-color: #d0cece;
                    color: #000000;
                    font-weight: bold;
                }
                
                /* Estilos para celdas de detalle */
                .excel-table .detail-cell {
                    background-color: #ffffff;
                    color: #000000;
                }
                
                /* Estilos para celdas de categoría */
                .excel-table .category-cell {
                    background-color: #c6e0b4;
                    color: #000000;
                    font-weight: bold;
                }
                
                /* Estilos para celdas de subcategoría */
                .excel-table .subcategory-cell {
                    background-color: #e2efda;
                    color: #000000;
                    font-weight: normal;
                }
                
                /* Estilos para celdas de grupo */
                .excel-table .group-cell {
                    background-color: #a9d18e;
                    color: #000000;
                    font-weight: bold;
                }
                
                /* Estilos para celdas de subgrupo */
                .excel-table .subgroup-cell {
                    background-color: #c6e0b4;
                    color: #000000;
                    font-weight: normal;
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