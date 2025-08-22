/**
 * ExcelModalEngine - Motor para visualizar archivos Excel en modales
 * @class
 */
class ExcelModalEngine {
    constructor() {
        this.sheetJSLoaded = false;
        this.loadingPromise = null;
    }

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
            script.onerror = () => {
                reject(new Error('No se pudo cargar SheetJS'));
            };
            document.head.appendChild(script);
        });

        return this.loadingPromise;
    }

    /**
     * Renderiza el archivo Excel en un contenedor
     */
    async renderExcelInContainer(containerId, excelUrl, fileName, pdfUrl = null) {
        const container = document.getElementById(containerId);
        if (!container) throw new Error(`Contener ${containerId} no encontrado`);

        this.showLoadingState(container, 'Cargando biblioteca...');
        try {
            await this.init();
            this.showLoadingState(container, 'Procesando archivo...');

            const arrayBuffer = await this.fetchExcelFile(excelUrl);
            const html = await this.processExcelFile(arrayBuffer, fileName, excelUrl, pdfUrl);
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

    /**
     * Procesa el archivo Excel y genera HTML
     */
    async processExcelFile(buffer, fileName, excelUrl, pdfUrl) {
        const workbook = XLSX.read(buffer, { type: 'array', cellStyles: true });
        const sheetName = workbook.SheetNames[0];
        const worksheet = workbook.Sheets[sheetName];

        const range = XLSX.utils.decode_range(worksheet['!ref'] || 'A1:A1');
        const usefulRange = this.calculateUsefulRange(worksheet, range);
        const colWidths = this.calculateColumnWidths(worksheet, usefulRange);
        const mergedMap = this.createMergedCellsMap(worksheet['!merges'] || []);

        const tableHTML = this.buildTableHTML(worksheet, usefulRange, colWidths, mergedMap);
        return this.wrapInContainer(tableHTML, fileName, excelUrl, pdfUrl);
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
        const minWidth = 80;
        const maxWidth = 400;
        const charWidth = 7;
        const padding = 16;

        const widths = {};
        for (let c = range.s.c; c <= range.e.c; c++) {
            let maxLen = 0;
            for (let r = range.s.r; r <= range.e.r; r++) {
                const ref = XLSX.utils.encode_cell({ r, c });
                const cell = worksheet[ref];
                if (cell && cell.v !== undefined && cell.v !== null) {
                    const val = this.formatCellValue(cell).toString();
                    if (val.includes('\n')) {
                        const lines = val.split('\n');
                        const longest = Math.max(...lines.map(l => l.length));
                        maxLen = Math.max(maxLen, longest);
                    } else {
                        maxLen = Math.max(maxLen, val.length);
                    }
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

    /**
     * Construye tabla HTML con formato
     */
    buildTableHTML(worksheet, range, colWidths, mergedMap) {
        const htmlParts = [];
        const dynamicStyles = [];

        // Colgroup
        htmlParts.push('<colgroup>');
        for (let c = range.s.c; c <= range.e.c; c++) {
            const width = colWidths[c] || 100;
            htmlParts.push(`<col style="width: ${width}px; min-width: ${width}px;">`);
        }
        htmlParts.push('</colgroup>');

        // Tabla
        htmlParts.push('<table class="table excel-table table-bordered">');

        for (let r = range.s.r; r <= range.e.r; r++) {
            const rowClasses = this.getRowClasses(r, range.e.r);
            htmlParts.push(`<tr class="${rowClasses}">`);

            for (let c = range.s.c; c <= range.e.c; c++) {
                const key = `${r}_${c}`;
                const mergeInfo = mergedMap[key];

                if (mergeInfo && !mergeInfo.isMaster) continue;

                const ref = XLSX.utils.encode_cell({ r, c });
                const cell = worksheet[ref];
                const cellData = this.processCellData(cell, ref);

                const tag = r <= 1 ? 'th' : 'td';
                const classes = [
                    ...cellData.classes,
                    cellData.hasCustomStyle ? `cell-${r}-${c}` : ''
                ].filter(Boolean).join(' ');

                const mergeAttrs = mergeInfo
                    ? `rowspan="${mergeInfo.rowspan}" colspan="${mergeInfo.colspan}"`
                    : '';

                const style = cellData.customStyle
                    ? `style="${cellData.customStyle}"`
                    : '';

                htmlParts.push(
                    `<${tag} class="${classes}" ${mergeAttrs} ${style}>${cellData.displayValue}</${tag}>`
                );

                if (cellData.hasCustomStyle) {
                    dynamicStyles.push(`.${classes.split(' ')[0]} { ${cellData.customStyle} }`);
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

    /**
     * Procesa datos de celda
     */
    processCellData(cell, ref) {
        if (!cell || cell.v === null || cell.v === undefined || cell.v === '') {
            return { displayValue: '&nbsp;', classes: ['empty-cell'], customStyle: '', hasCustomStyle: false };
        }

        const value = this.formatCellValue(cell);
        const contentStr = value.toString();

        const classes = [
            contentStr.length > 30 || contentStr.includes('\n') ? 'long-text-cell' : '',
            contentStr.length > 15 ? 'medium-text-cell' : '',
            contentStr.length <= 15 ? 'short-text-cell' : '',
            cell.t === 'n' ? 'number-cell' : 'text-cell'
        ].filter(Boolean);

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

        return { displayValue: value, classes, customStyle, hasCustomStyle };
    }

    /**
     * Formatea valor de celda
     */
    formatCellValue(cell) {
        if (cell.v === undefined || cell.v === null) return '';
        if (cell.t === 'n') {
            if (cell.z?.includes('%')) return (cell.v * 100).toFixed(2) + '%';
            if (cell.z?.includes('$')) return '$' + cell.v.toLocaleString('en-US', { minimumFractionDigits: 2 });
            if (cell.z?.includes(',')) return cell.v.toLocaleString();
            return cell.v.toString();
        }
        return cell.v.toString();
    }

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
        return rowIndex === 0 ? 'header-row' : rowIndex === lastRow ? 'footer-row' : 'data-row';
    }

    /**
     * Envuelve tabla en contenedores con botones y estilos
     */
    wrapInContainer(tableHTML, fileName, excelUrl, pdfUrl) {
        let buttons = '';
        if (pdfUrl) {
            buttons += `<a href="${pdfUrl}" class="btn btn-sm btn-outline-danger me-2" target="_blank" download>
                <i class="bi bi-file-pdf me-1"></i>Descargar PDF
            </a>`;
        }
        if (excelUrl) {
            buttons += `<a href="${excelUrl}" class="btn btn-sm btn-outline-success" download>
                <i class="bi bi-file-excel me-1"></i>Descargar Excel
            </a>`;
        }

        return `
            <div class="excel-viewer-container">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">
                        <i class="bi bi-file-excel text-success me-2"></i>
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
     * Estilos CSS globales para tabla
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
                    max-width: none;
                    min-width: 120px;
                    text-align: left;
                    padding: 6px 10px;
                    line-height: 1.4;
                }

                .excel-table .medium-text-cell {
                    white-space: normal;
                    word-wrap: break-word;
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
                    word-wrap: break-word;
                    text-align: center !important;
                    vertical-align: middle;
                    font-weight: bold;
                    padding: 6px 10px;
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

                .excel-table .header-row:nth-child(2) th {
                    background-color: #e2efda;
                    color: #000000;
                    font-weight: bold;
                    text-align: center;
                }

                .excel-table .number-cell:not(.merged-cell) {
                    text-align: right;
                    background-color: #e2efda;
                    white-space: nowrap;
                }

                .excel-table .full-row-merged {
                    background-color: #70ad47 !important;
                    color: #ffffff !important;
                    font-weight: bold;
                    text-align: center !important;
                    font-size: 12px;
                    padding: 8px !important;
                    white-space: pre-wrap;
                    word-wrap: break-word;
                }

                .excel-table tbody tr:nth-child(even) td:not(.merged-cell):not(.full-row-merged) {
                    background-color: #f2f8ec;
                }

                .excel-table tbody tr:hover td:not(.merged-cell):not(.full-row-merged) {
                    background-color: #d5e8d4 !important;
                    transition: background-color 0.2s ease;
                }

                /* Primera columna */
                .excel-table td:first-child:not(.merged-cell) {
                    background-color: #c6e0b4;
                    font-weight: bold;
                    text-align: left;
                    padding-left: 12px;
                    white-space: pre-wrap;
                    word-wrap: break-word;
                    min-width: 90px !important; /* ¡Clave! */
                }

                /* Contenedores */
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

                /* Responsive */
                @media (max-width: 768px) {
                    .excel-table {
                        font-size: 10px;
                    }
                    .excel-table th, .excel-table td {
                        padding: 3px 6px;
                    }
                }
            </style>
        `;
    }

    showLoadingState(container, message) {
        container.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-success" role="status"></div>
                <p class="mt-3">${message}</p>
            </div>
        `;
    }

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