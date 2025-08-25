/**
 * ExcelModalEngine - Motor para visualizar archivos Excel en modales
 * @class
 */
class ExcelModalEngine {
    constructor() {
        this.sheetJSLoaded = false;
        this.loadingPromise = null;
    }

    // === CARGA Y RENDERIZADO ===

    /**
     * Inicializa SheetJS si no está cargado
     */
    async init() {
        if (typeof XLSX !== 'undefined') {
            this.sheetJSLoaded = true;
            return Promise.resolve();
        }

        if (this.loadingPromise) return this.loadingPromise;

        this.loadingPromise = new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js';
            script.onload = () => {
                this.sheetJSLoaded = true;
                resolve();
            };
            script.onerror = () => reject(new Error('No se pudo cargar SheetJS'));
            document.head.appendChild(script);
        });

        return this.loadingPromise;
    }

    /**
     * Renderiza el archivo Excel en un contenedor
     */
    async renderExcelInContainer(containerId, excelUrl, fileName, pdfUrl = null, excelFormatedUrl = null) {
        const container = document.getElementById(containerId);
        if (!container) throw new Error(`Contenedor ${containerId} no encontrado`);

        this.showLoadingState(container, 'Cargando biblioteca...');
        try {
            await this.init();
            this.showLoadingState(container, 'Procesando archivo...');

            const arrayBuffer = await this.fetchExcelFile(excelUrl);
            const html = await this.processExcelFile(arrayBuffer, fileName, excelUrl, pdfUrl, excelFormatedUrl);
            container.innerHTML = html;
        } catch (error) {
            console.error('Error:', error);
            this.showErrorState(container, error.message, excelUrl);
        }
    }

    /**
     * Descarga el archivo Excel
     */
    async fetchExcelFile(url) {
        const res = await fetch(url);
        if (!res.ok) throw new Error(`Error al cargar archivo (${res.status})`);
        return await res.arrayBuffer();
    }

    // === PROCESAMIENTO DE DATOS ===

    /**
     * Procesa el archivo Excel y genera HTML
     */
    async processExcelFile(buffer, fileName, excelUrl, pdfUrl, excelFormatedUrl = null) {
        const workbook = XLSX.read(buffer, { 
            type: 'array', 
            cellStyles: true,
            cellNF: true,    // Preserva formatos numéricos
            cellText: false  // Permite formato personalizado
        });
        
        const sheetName = workbook.SheetNames[0];
        const worksheet = workbook.Sheets[sheetName];

        const range = XLSX.utils.decode_range(worksheet['!ref'] || 'A1:A1');
        const usefulRange = this.calculateUsefulRange(worksheet, range);
        const colWidths = this.calculateColumnWidths(worksheet, usefulRange);
        const mergedMap = this.createMergedCellsMap(worksheet['!merges'] || []);

        const tableHTML = this.buildTableHTML(worksheet, usefulRange, colWidths, mergedMap);
        return this.wrapInContainer(tableHTML, fileName, excelUrl, pdfUrl, excelFormatedUrl);
    }

    /**
     * Calcula el rango útil (elimina filas/columnas vacías)
     */
    calculateUsefulRange(worksheet, range) {
        let minRow = range.e.r, maxRow = range.s.r;
        let minCol = range.e.c, maxCol = range.s.c;

        for (let r = range.s.r; r <= range.e.r; r++) {
            for (let c = range.s.c; c <= range.e.c; c++) {
                const ref = XLSX.utils.encode_cell({ r, c });
                const cell = worksheet[ref];
                if (cell && cell.v !== undefined && cell.v !== null && cell.v !== '') {
                    minRow = Math.min(minRow, r);
                    maxRow = Math.max(maxRow, r);
                    minCol = Math.min(minCol, c);
                    maxCol = Math.max(maxCol, c);
                }
            }
        }

        if (minRow > maxRow || minCol > maxCol) return range;
        return { s: { r: minRow, c: minCol }, e: { r: maxRow, c: maxCol } };
    }

    /**
     * Calcula anchos de columnas basados en contenido
     */
    calculateColumnWidths(worksheet, range) {
        const widths = {};
        const minWidth = 80;
        const maxWidth = 400;
        const charWidth = 7;
        const padding = 16;

        for (let c = range.s.c; c <= range.e.c; c++) {
            let maxLen = 0;
            for (let r = range.s.r; r <= range.e.r; r++) {
                const ref = XLSX.utils.encode_cell({ r, c });
                const cell = worksheet[ref];
                if (cell && cell.v !== undefined && cell.v !== null) {
                    const val = this.getDisplayValue(cell).toString();
                    const len = val.includes('\n') 
                        ? Math.max(...val.split('\n').map(l => l.length))
                        : val.length;
                    maxLen = Math.max(maxLen, len);
                }
            }

            let width = maxLen * charWidth + padding;
            width = Math.max(minWidth, Math.min(maxWidth, width));
            widths[c] = Math.round(width);
        }
        return widths;
    }

    /**
     * Crea mapa de celdas combinadas
     */
    createMergedCellsMap(mergedCells) {
        const map = {};
        mergedCells.forEach(merge => {
            for (let r = merge.s.r; r <= merge.e.r; r++) {
                for (let c = merge.s.c; c <= merge.e.c; c++) {
                    map[`${r}_${c}`] = {
                        isMaster: r === merge.s.r && c === merge.s.c,
                        rowspan: merge.e.r - merge.s.r + 1,
                        colspan: merge.e.c - merge.s.c + 1
                    };
                }
            }
        });
        return map;
    }

    // === GENERACIÓN DE HTML ===

    /**
     * Construye tabla HTML con formato
     */
    buildTableHTML(worksheet, range, colWidths, mergedMap) {
        const htmlParts = [];
        const dynamicStyles = [];

        // Colgroup con ancho mínimo garantizado para primera columna
        htmlParts.push('<colgroup>');
        for (let c = range.s.c; c <= range.e.c; c++) {
            const width = colWidths[c] || 100;
            // Garantizar ancho mínimo para primera columna
            const minWidth = c === range.s.c ? Math.max(width, 120) : width;
            htmlParts.push(`<col style="width: ${minWidth}px; min-width: ${minWidth}px;">`);
        }
        htmlParts.push('</colgroup>');

        // Tabla
        htmlParts.push('<table class="table excel-table table-bordered">');

        for (let r = range.s.r; r <= range.e.r; r++) {
            htmlParts.push(`<tr class="${this.getRowClasses(r, range.e.r)}">`);

            for (let c = range.s.c; c <= range.e.c; c++) {
                const key = `${r}_${c}`;
                const mergeInfo = mergedMap[key];

                if (mergeInfo && !mergeInfo.isMaster) continue;

                const ref = XLSX.utils.encode_cell({ r, c });
                const cell = worksheet[ref];
                const cellData = this.processCellData(cell, ref);

                const tag = r <= 1 ? 'th' : 'td';
                const classes = this.buildCellClasses(cellData, r, c);
                const mergeAttrs = this.buildMergeAttributes(mergeInfo);
                const style = cellData.customStyle ? `style="${cellData.customStyle}"` : '';

                htmlParts.push(
                    `<${tag} class="${classes}" ${mergeAttrs} ${style}>${cellData.displayValue}</${tag}>`
                );

                if (cellData.hasCustomStyle && cellData.classes.length > 0) {
                    const className = `cell-${r}-${c}`;
                    dynamicStyles.push(`.${className} { ${cellData.customStyle} }`);
                }
            }

            htmlParts.push('</tr>');
        }

        htmlParts.push('</table>');

        if (dynamicStyles.length) {
            htmlParts.unshift(`<style>${dynamicStyles.join('\n')}</style>`);
        }

        return htmlParts.join('');
    }

    // === PROCESAMIENTO DE CELDAS ===

    /**
     * Procesa datos de celda
     */
    processCellData(cell, ref) {
        if (!cell || cell.v === null || cell.v === undefined || cell.v === '') {
            return { 
                displayValue: '&nbsp;', 
                classes: ['empty-cell'], 
                customStyle: '', 
                hasCustomStyle: false 
            };
        }

        const displayValue = this.getDisplayValue(cell);
        const contentStr = displayValue.toString();
        const classes = this.getContentClasses(contentStr, cell);
        let customStyle = '';
        let hasCustomStyle = false;

        if (cell.s) {
            const styles = this.extractCellStyles(cell.s);
            customStyle = styles.cssString;
            hasCustomStyle = styles.hasStyles;
            classes.push(...styles.classes);
        }

        // Forzar centrado en celdas combinadas
        if (cell && cell.merged && cell.merged.master) {
            classes.push('merged-cell');
            if (!customStyle.includes('text-align')) {
                customStyle += customStyle ? '; text-align: center' : 'text-align: center';
                hasCustomStyle = true;
            }
        }

        return { displayValue, classes, customStyle, hasCustomStyle };
    }

    /**
     * Obtiene el valor formateado de la celda (respeta fórmulas y formatos)
     */
    getDisplayValue(cell) {
        // Si hay valor formateado (w), usarlo (respeta redondeos y fórmulas)
        if (cell.w !== undefined) return cell.w;
        
        // Si no, formatear según tipo
        if (cell.v === undefined || cell.v === null) return '';
        
        if (cell.t === 'n') {
            if (cell.z) {
                // Usar formato numérico si está disponible
                return XLSX.SSF.format(cell.z, cell.v);
            }
            return cell.v.toString();
        }
        
        return cell.v.toString();
    }

    /**
     * Determina clases según contenido
     */
    getContentClasses(contentStr, cell) {
        const classes = [];
        
        // Clases por longitud de contenido
        if (contentStr.length > 30 || contentStr.includes('\n')) {
            classes.push('long-text-cell');
        } else if (contentStr.length > 15) {
            classes.push('medium-text-cell');
        } else {
            classes.push('short-text-cell');
        }
        
        // Clases por tipo de dato
        classes.push(cell.t === 'n' ? 'number-cell' : 'text-cell');
        
        return classes;
    }

    /**
     * Construye clases CSS para celda
     */
    buildCellClasses(cellData, row, col) {
        const baseClasses = cellData.classes.filter(Boolean);
        if (cellData.hasCustomStyle) {
            baseClasses.push(`cell-${row}-${col}`);
        }
        return baseClasses.join(' ');
    }

    /**
     * Construye atributos de combinación
     */
    buildMergeAttributes(mergeInfo) {
        if (!mergeInfo) return '';
        const attrs = [];
        if (mergeInfo.rowspan > 1) attrs.push(`rowspan="${mergeInfo.rowspan}"`);
        if (mergeInfo.colspan > 1) attrs.push(`colspan="${mergeInfo.colspan}"`);
        return attrs.join(' ');
    }

    // === ESTILOS Y FORMATOS ===

    /**
     * Extrae estilos de celda
     */
    extractCellStyles(style) {
        const css = [];
        const classes = [];

        // Fuente
        if (style.font) {
            if (style.font.bold) css.push('font-weight: bold');
            if (style.font.italic) css.push('font-style: italic');
            if (style.font.underline) css.push('text-decoration: underline');
            if (style.font.sz) css.push(`font-size: ${style.font.sz}px`);
            if (style.font.color?.rgb) css.push(`color: #${style.font.color.rgb}`);
            if (style.font.name) css.push(`font-family: '${style.font.name}', sans-serif`);
        }

        // Alineación
        if (style.alignment) {
            const alignMap = { left: 'left', center: 'center', right: 'right', justify: 'justify' };
            const vAlignMap = { top: 'top', middle: 'middle', bottom: 'bottom' };
            
            const hAlign = alignMap[style.alignment.horizontal] || style.alignment.horizontal;
            const vAlign = vAlignMap[style.alignment.vertical] || style.alignment.vertical;
            
            if (hAlign) css.push(`text-align: ${hAlign}`);
            if (vAlign) css.push(`vertical-align: ${vAlign}`);
            
            if (style.alignment.wrapText) {
                css.push('white-space: pre-wrap');
                css.push('word-wrap: break-word');
            }
        }

        // Fondo
        if (style.fill && style.fill.fgColor?.rgb) {
            css.push(`background-color: #${style.fill.fgColor.rgb}`);
        }

        // Bordes
        if (style.border) {
            const sides = ['top', 'right', 'bottom', 'left'];
            const borderStyleMap = {
                thin: '1px solid',
                medium: '2px solid',
                thick: '3px solid',
                dotted: '1px dotted',
                dashed: '2px dashed',
                none: 'none',
                default: '1px solid'
            };

            const getColor = (border) => {
                if (!border) return '#000000';
                if (border.color?.rgb) return `#${border.color.rgb}`;
                if (border.color?.indexed === 0) return '#000000';
                if (border.color?.indexed === 1) return '#FFFFFF';
                return '#000000';
            };

            sides.forEach(side => {
                if (style.border[side]) {
                    const styleName = borderStyleMap[style.border[side].style] || borderStyleMap.default;
                    const color = getColor(style.border[side]);
                    css.push(`border-${side}: ${styleName} ${color}`);
                }
            });

            if (style.border.diagonal) {
                classes.push('diagonal-border');
                css.push('position: relative');
            }
        }

        // Formatos numéricos
        if (style.numFmt) {
            if (style.numFmt.includes('%')) classes.push('percentage-format');
            if (style.numFmt.includes('$') || style.numFmt.includes('€')) classes.push('currency-format');
            if (style.numFmt.includes('yyyy') || style.numFmt.includes('date')) classes.push('date-format');
        }

        return {
            cssString: css.join('; '),
            classes: classes.filter(Boolean),
            hasStyles: css.length > 0
        };
    }

    /**
     * Clases para filas
     */
    getRowClasses(rowIndex, lastRow) {
        if (rowIndex === 0) return 'header-row';
        if (rowIndex === lastRow) return 'footer-row';
        return 'data-row';
    }

    // === RENDERIZADO FINAL ===

    /**
     * Envuelve tabla en contenedores con botones
     */
    wrapInContainer(tableHTML, fileName, excelUrl, pdfUrl, excelFormatedUrl) {
        //console.log('BLADE: excelUrl', excelUrl);
        //console.log('BLADE: excelFormatedUrl', excelFormatedUrl);
        const buttons = this.buildDownloadButtons(excelUrl, pdfUrl, excelFormatedUrl);
        return `
            <div class="excel-viewer-container">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">
                        <i class="bi bi-file-excel text-primary me-2"></i>
                        Archivo Excel: ${fileName || ''}
                    </h5>
                    <div class="btn-group">${buttons}</div>
                </div>
                <div class="table-responsive excel-table-wrapper">
                    ${this.getTableStyles()}
                    ${tableHTML}
                </div>
            </div>
        `;
    }

    /**
     * Construye botones de descarga
     */
    buildDownloadButtons(excelUrl, pdfUrl, excelFormatedUrl = null) {
        let buttons = '';
        
        if (pdfUrl) {
            buttons += `
                <a href="${pdfUrl}" class="btn btn-sm btn-outline-danger me-2 rounded-4" target="_blank" download>
                    <i class="bi bi-file-pdf me-1"></i>Descargar PDF
                </a>
            `;
        }
        
        if (excelUrl) {
            buttons += `
                <a href="${excelUrl}" class="btn btn-sm btn-outline-primary rounded-4 me-2" download>
                    <i class="bi bi-table"></i> * Descargar dataset *
                </a>
            `;
        }
        
        if (excelFormatedUrl) {
            buttons += `
                <a href="${excelFormatedUrl}" class="btn btn-sm btn-outline-success rounded-4" download>
                    <i class="bi bi-file-earmark-excel"></i> Descargar Excel Formateado
                </a>
            `;
        }

        return buttons;
    }

    /**
     * Estilos CSS globales para tabla
     */
    getTableStyles() {
        return `<style>
        .short-text-cell, .number-cell {
            background-color: #ff0000;
        }
        </style>`;
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
                    <i class="bi bi-download me-2"></i>Descargar directamente
                </a>
            </div>
        `;
    }
}

// Instancia global
window.ExcelModalEngine = new ExcelModalEngine();