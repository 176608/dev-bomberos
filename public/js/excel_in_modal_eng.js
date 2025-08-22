/**
 * ExcelModalEngine - Motor para renderizar archivos Excel en modales
 */
class ExcelModalEngine {
    constructor() {
        this.sheetJSLoaded = false;
        this.loadingPromise = null;
    }

    /**
     * Inicializa SheetJS si aún no está cargado
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
            script.onerror = () => {
                reject(new Error('No se pudo cargar SheetJS'));
            };
            document.head.appendChild(script);
        });

        return this.loadingPromise;
    }

    /**
     * Renderiza un archivo Excel en un contenedor específico
     */
    async renderExcelInContainer(containerId, excelUrl, fileName, pdfUrl = null) {
        const container = document.getElementById(containerId);
        if (!container) throw new Error(`Contenedores ${containerId} no encontrado`);

        this.showLoadingState(container, 'Cargando biblioteca...');
        try {
            await this.init();
            this.showLoadingState(container, 'Procesando archivo Excel...');

            const arrayBuffer = await this.fetchExcelFile(excelUrl);
            const htmlContent = await this.processExcelFile(arrayBuffer, fileName, excelUrl, pdfUrl);
            container.innerHTML = htmlContent;
        } catch (error) {
            console.error('Error al renderizar Excel:', error);
            this.showErrorState(container, error.message, excelUrl);
        }
    }

    /**
     * Carga el archivo Excel como ArrayBuffer
     */
    async fetchExcelFile(url) {
        const response = await fetch(url);
        if (!response.ok) throw new Error(`Error al cargar Excel (${response.status})`);
        return await response.arrayBuffer();
    }

    /**
     * Procesa el archivo Excel y genera HTML con formato
     */
    async processExcelFile(arrayBuffer, fileName, excelUrl, pdfUrl) {
        const data = new Uint8Array(arrayBuffer);
        const workbook = XLSX.read(data, { type: 'array', cellStyles: true });
        const sheetName = workbook.SheetNames[0];
        const worksheet = workbook.Sheets[sheetName];

        const tableHTML = this.createFormattedTable(worksheet, workbook);
        return this.wrapInContainer(tableHTML, fileName, excelUrl, pdfUrl);
    }

    /**
     * Crea una tabla HTML con formato preservado
     */
    createFormattedTable(worksheet, workbook) {
        const range = XLSX.utils.decode_range(worksheet['!ref'] || 'A1:A1');
        const mergedCells = worksheet['!merges'] || [];
        const usefulRange = this.calculateUsefulRange(worksheet, range);
        const columnWidths = this.calculateColumnWidths(worksheet, usefulRange, workbook);
        const mergedMap = this.createMergedCellsMap(mergedCells);

        let html = '<table class="table excel-table table-bordered">';
        html += '<colgroup>';
        for (let c = usefulRange.s.c; c <= usefulRange.e.c; c++) {
            const width = columnWidths[c] || 100;
            html += `<col style="width: ${width}px; min-width: ${width}px;">`;
        }
        html += '</colgroup>';

        for (let r = usefulRange.s.r; r <= usefulRange.e.r; r++) {
            const rowClasses = this.getRowClasses(r, usefulRange.e.r);
            html += `<tr class="${rowClasses}">`;

            for (let c = usefulRange.s.c; c <= usefulRange.e.c; c++) {
                const cellKey = `${r}_${c}`;
                const mergeInfo = mergedMap[cellKey];
                if (mergeInfo && !mergeInfo.isMaster) continue;

                const cellRef = XLSX.utils.encode_cell({ r, c });
                const cell = worksheet[cellRef];
                const cellData = this.processCellData(cell, cellRef, workbook);

                if (mergeInfo && mergeInfo.isMaster) {
                    cellData.classes.push('merged-cell');
                    if (mergeInfo.colspan === (usefulRange.e.c - usefulRange.s.c + 1)) {
                        cellData.classes.push('full-row-merged');
                    }
                    if (!cellData.customStyle.includes('text-align')) {
                        cellData.customStyle += ' text-align: center';
                        cellData.hasCustomStyle = true;
                    }
                }

                const tag = r <= 1 ? 'th' : 'td';
                const attrs = this.getMergeAttributes(mergeInfo);
                const styleId = cellData.hasCustomStyle ? `cell-${r}-${c}` : '';
                const classes = [...cellData.classes, styleId].filter(Boolean).join(' ');

                html += `<${tag} class="${classes}" style="${cellData.customStyle}"${attrs}>${cellData.displayValue}</${tag}>`;
            }
            html += '</tr>';
        }
        html += '</table>';

        // Agregar estilos dinámicos
        const dynamicStyles = [];
        for (let r = usefulRange.s.r; r <= usefulRange.e.r; r++) {
            for (let c = usefulRange.s.c; c <= usefulRange.e.c; c++) {
                const key = `cell-${r}-${c}`;
                const cellRef = XLSX.utils.encode_cell({ r, c });
                const cell = worksheet[cellRef];
                if (!cell || !cell.s) continue;
                const style = this.extractCellStyles(cell.s);
                if (style.cssString) {
                    dynamicStyles.push(`.${key} { ${style.cssString} }`);
                }
            }
        }

        if (dynamicStyles.length > 0) {
            html = `<style>${dynamicStyles.join('\n')}</style>${html}`;
        }

        return html;
    }

    /**
     * Calcula el rango útil eliminando filas/columnas vacías
     */
    calculateUsefulRange(worksheet, originalRange) {
        let minRow = originalRange.e.r, maxRow = originalRange.s.r;
        let minCol = originalRange.e.c, maxCol = originalRange.s.c;

        for (let r = originalRange.s.r; r <= originalRange.e.r; r++) {
            for (let c = originalRange.s.c; c <= originalRange.e.c; c++) {
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

        if (minRow > maxRow || minCol > maxCol) return originalRange;

        return { s: { r: minRow, c: minCol }, e: { r: maxRow, c: maxCol } };
    }

    /**
     * Calcula anchos de columnas basados en contenido
     */
    calculateColumnWidths(worksheet, range, workbook) {
        const widths = {};
        const minWidth = 80;
        const maxWidth = 400;
        const charWidth = 7;
        const padding = 16;

        for (let c = range.s.c; c <= range.e.c; c++) {
            let maxLen = 0;
            let hasLongText = false;
            let maxWords = 0;

            for (let r = range.s.r; r <= range.e.r; r++) {
                const ref = XLSX.utils.encode_cell({ r, c });
                const cell = worksheet[ref];
                if (!cell || cell.v === null || cell.v === undefined) continue;

                const value = this.formatCellValue(cell);
                const str = value.toString();

                if (str.includes('\n') || str.length > 30) hasLongText = true;
                maxWords = Math.max(maxWords, str.split(/\s+/).filter(w => w.length > 0).length);

                if (str.includes('\n')) {
                    const lines = str.split('\n');
                    const longest = Math.max(...lines.map(l => l.length));
                    maxLen = Math.max(maxLen, longest);
                } else {
                    maxLen = Math.max(maxLen, str.length);
                }
            }

            let width = maxLen * charWidth;
            if (hasLongText && maxWords > 3) {
                width = Math.min(width, 250);
            } else if (maxLen > 20) {
                width *= 0.9;
            }
            width += padding;
            widths[c] = Math.round(Math.max(minWidth, Math.min(maxWidth, width)));
        }

        return widths;
    }

    /**
     * Procesa datos y formato de celda
     */
    processCellData(cell, cellRef, workbook) {
        if (!cell || cell.v === undefined || cell.v === null || cell.v === '') {
            return { displayValue: '&nbsp;', classes: ['empty-cell'], customStyle: '', hasCustomStyle: false };
        }

        const displayValue = this.formatCellValue(cell);
        const contentStr = displayValue.toString();
        const classes = [];

        if (contentStr.length > 30 || contentStr.includes('\n')) {
            classes.push('long-text-cell');
        } else if (contentStr.length > 15) {
            classes.push('medium-text-cell');
        } else {
            classes.push('short-text-cell');
        }

        if (cell.t === 'n') {
            classes.push('number-cell');
        } else {
            classes.push('text-cell');
        }

        let customStyle = '';
        let hasCustomStyle = false;

        if (cell.s) {
            const style = this.extractCellStyles(cell.s);
            customStyle = style.cssString;
            hasCustomStyle = style.hasStyles;
            classes.push(...style.classes);
        }

        return { displayValue, classes, customStyle, hasCustomStyle };
    }

    /**
     * Formatea valores de celdas según tipo y formato
     */
    formatCellValue(cell) {
        if (cell.v === undefined || cell.v === null) return '';

        if (cell.t === 'n') {
            if (cell.z) {
                if (cell.z.includes('%')) return (cell.v * 100).toFixed(2) + '%';
                if (cell.z.includes('$')) return '$' + cell.v.toLocaleString('en-US', { minimumFractionDigits: 2 });
                if (cell.z.includes(',')) return cell.v.toLocaleString();
            }
            return cell.v.toString();
        }

        return cell.v.toString();
    }

    /**
     * Extrae estilos CSS de una celda
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
            if (style.font.color && style.font.color.rgb) css.push(`color: #${style.font.color.rgb}`);
            if (style.font.name) css.push(`font-family: '${style.font.name}', sans-serif`);
        }

        // Alineación
        if (style.alignment) {
            const hAlignMap = { left: 'left', center: 'center', right: 'right', justify: 'justify' };
            const vAlignMap = { top: 'top', middle: 'middle', bottom: 'bottom' };
            const hAlign = hAlignMap[style.alignment.horizontal] || style.alignment.horizontal;
            const vAlign = vAlignMap[style.alignment.vertical] || style.alignment.vertical;

            if (hAlign) css.push(`text-align: ${hAlign}`);
            if (vAlign) css.push(`vertical-align: ${vAlign}`);
            if (style.alignment.wrapText) {
                css.push('white-space: pre-wrap');
                css.push('word-wrap: break-word');
            }
        }

        // Fondo
        if (style.fill && style.fill.fgColor && style.fill.fgColor.rgb) {
            css.push(`background-color: #${style.fill.fgColor.rgb}`);
        }

        // Bordes
        if (style.border) {
            const borderStyleMap = {
                thin: '1px solid',
                medium: '2px solid',
                thick: '3px solid',
                dotted: '1px dotted',
                dashed: '2px dashed',
                hair: '0.5px solid',
                mediumDashed: '2px dashed',
                thickDashed: '3px dashed',
                mediumDotted: '2px dotted',
                dashDot: '1px dashed',
                mediumDashDot: '2px dashed',
                dashDotDot: '1px dotted',
                mediumDashDotDot: '2px dotted',
                slantDashDot: '2px dashed',
                double: '3px double',
                none: 'none',
                default: '1px solid'
            };

            const getBorderStyle = (border) => {
                if (!border || !border.style) return null;
                const style = borderStyleMap[border.style] || borderStyleMap.default;
                let color = '#000000';
                if (border.color && border.color.rgb) color = `#${border.color.rgb}`;
                return `${style} ${color}`;
            };

            const sides = ['top', 'right', 'bottom', 'left'];
            sides.forEach(side => {
                if (style.border[side]) {
                    const bs = getBorderStyle(style.border[side]);
                    if (bs && bs !== 'none') css.push(`border-${side}: ${bs}`);
                }
            });

            if (style.border.diagonal) {
                classes.push('diagonal-border');
                css.push('position: relative');
            }
        }

        // Formatos numéricos especiales
        if (style.numFmt) {
            if (style.numFmt.includes('%')) classes.push('percentage-format');
            if (style.numFmt.includes('$') || style.numFmt.includes('€')) classes.push('currency-format');
            if (style.numFmt.includes('date') || style.numFmt.includes('yyyy')) classes.push('date-format');
        }

        return {
            cssString: css.join('; '),
            classes,
            hasStyles: css.length > 0
        };
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

    /**
     * Clases para filas
     */
    getRowClasses(rowIndex, lastRowIndex) {
        return rowIndex === 0 ? 'header-row' : rowIndex === lastRowIndex ? 'footer-row' : 'data-row';
    }

    /**
     * Atributos para celdas combinadas
     */
    getMergeAttributes(mergeInfo) {
        if (!mergeInfo || !mergeInfo.isMaster) return '';
        return mergeInfo.rowspan > 1 ? ` rowspan="${mergeInfo.rowspan}"` : '';
    }

    /**
     * Envuelve la tabla en contenedores con botones y estilos
     */
    wrapInContainer(tableHTML, fileName, excelUrl, pdfUrl) {
        let buttons = '';
        if (pdfUrl) {
            buttons += `<a href="${pdfUrl}" class="btn btn-sm btn-outline-danger me-2" target="_blank" download><i class="bi bi-file-pdf me-1"></i>Descargar PDF</a>`;
        }
        if (excelUrl) {
            buttons += `<a href="${excelUrl}" class="btn btn-sm btn-outline-success" download><i class="bi bi-file-excel me-1"></i>Descargar Excel</a>`;
        }

        return `
            <div class="excel-viewer-container">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><i class="bi bi-file-excel text-success me-2"></i>Archivo Excel: ${fileName}</h5>
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
     * Estilos CSS para la tabla
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
                    table-layout: auto !important;
                    background-color: #ffffff;
                    border: 1px solid #d0d0d0;
                }

                .excel-table th, .excel-table td {
                    padding: 4px 8px;
                    text-align: center;
                    vertical-align: middle;
                    border: 1px solid #d0d0d0;
                    line-height: 1.3;
                    position: relative;
                    word-wrap: break-word;
                    overflow-wrap: break-word;
                }

                .excel-table .long-text-cell {
                    white-space: pre-wrap;
                    word-wrap: break-word;
                    text-align: left;
                    padding: 6px 10px;
                    line-height: 1.4;
                }

                .excel-table .medium-text-cell {
                    white-space: normal;
                    text-align: left;
                    padding: 4px 8px;
                }

                .excel-table .short-text-cell {
                    white-space: nowrap;
                    text-overflow: ellipsis;
                    overflow: hidden;
                }

                .excel-table .merged-cell {
                    white-space: pre-wrap;
                    text-align: center !important;
                    vertical-align: middle;
                    font-weight: bold;
                    padding: 6px 10px;
                }

                .excel-table .full-row-merged {
                    background-color: #70ad47 !important;
                    color: #ffffff !important;
                    font-weight: bold;
                    text-align: center !important;
                    font-size: 12px;
                    padding: 8px !important;
                    white-space: pre-wrap;
                }

                .excel-table .header-row th {
                    background-color: #70ad47;
                    color: #ffffff;
                    font-weight: bold;
                    text-align: center;
                    white-space: pre-wrap;
                    padding: 6px 8px;
                    line-height: 1.2;
                }

                .excel-table .header-row:nth-child(2) th,
                .excel-table tr:nth-child(2) th {
                    background-color: #e2efda;
                    color: #000000;
                    font-weight: bold;
                    text-align: center;
                }

                .excel-table td:first-child:not(.merged-cell) {
                    background-color: #c6e0b4;
                    font-weight: bold;
                    text-align: left;
                    padding-left: 12px;
                    white-space: pre-wrap;
                    word-wrap: break-word;
                }

                .excel-table .number-cell:not(.merged-cell) {
                    text-align: right;
                    background-color: #e2efda;
                    white-space: nowrap;
                }

                .excel-table .number-cell.merged-cell {
                    text-align: center !important;
                }

                .excel-table tbody tr:nth-child(even) td:not(.merged-cell):not(.full-row-merged) {
                    background-color: #f2f8ec;
                }

                .excel-table tbody tr:nth-child(even) td:first-child:not(.merged-cell) {
                    background-color: #d4e6c7;
                }

                .excel-table tbody tr:hover td:not(.merged-cell):not(.full-row-merged) {
                    background-color: #d5e8d4 !important;
                    transition: background-color 0.2s ease;
                }

                .excel-table colgroup col {
                    width: auto !important;
                    min-width: 80px;
                }

                .excel-viewer-container {
                    max-height: 85vh;
                    overflow: auto;
                    padding: 15px;
                    background-color: #ffffff;
                    border: 1px solid #d4d4d4;
                    border-radius: 4px;
                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                }

                .excel-table-wrapper {
                    width: auto;
                    max-width: 100%;
                    overflow-x: auto;
                    margin: 0 auto;
                    background-color: #ffffff;
                }

                .excel-table .diagonal-border::before {
                    content: '';
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    border-top: 1px solid #000;
                    transform-origin: top left;
                    transform: rotate(45deg);
                    pointer-events: none;
                    z-index: 1;
                }

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
                    white-space: nowrap;
                }

                .excel-table .empty-cell {
                    background-color: #f8f9fa;
                    min-width: 30px;
                    height: 20px;
                }

                @media (max-width: 768px) {
                    .excel-table {
                        font-size: 10px;
                    }
                    .excel-table th, .excel-table td {
                        padding: 3px 6px;
                    }
                    .excel-table .long-text-cell {
                        min-width: 100px;
                        padding: 4px 6px;
                    }
                }
            </style>
        `;
    }

    /**
     * Muestra estado de carga
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
     * Muestra estado de error
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