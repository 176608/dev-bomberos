/**
 * GraficaModalEngine - Motor para visualizar gráficas en modales desde Excel
 * @class
 */
class GraficaModalEngine {
    constructor() {
        this.chart = null;
        this.data = null;
        this.chartInstance = null;
    }

    /**
     * Renderiza una gráfica en el contenedores dado, procesando el Excel
     */
    async renderGraficaInContainer(containerId, excelUrl, fileName) {
        const container = document.getElementById(containerId);
        if (!container) return;

        // Mostrar estado de carga
        container.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-success" role="status"></div>
                <p class="mt-3">Procesando archivo Excel...</p>
            </div>
        `;

        try {
            await this.loadSheetJS();
            const arrayBuffer = await this.fetchExcelFile(excelUrl);
            const workbook = XLSX.read(arrayBuffer, { type: 'array', cellStyles: true });
            const worksheet = workbook.Sheets[workbook.SheetNames[0]];
            const range = XLSX.utils.decode_range(worksheet['!ref'] || 'A1:A1');
            const dataMatrix = this.extractAndCleanData(worksheet, range);

            //console.log('📊 Matriz limpia:', dataMatrix);

            // Extraer tipo de gráfica y datos estructurados
            const { tipoGrafica, CabeceraY, RowsY, GroupColsX, ColsX } = this.parseDataStructure(dataMatrix);

            // Renderizar interfaz de selección
            this.renderSelectionInterface(container, dataMatrix, fileName, excelUrl, {
                tipoGrafica,
                CabeceraY,
                RowsY,
                GroupColsX,
                ColsX
            });

        } catch (error) {
            console.error('Error al cargar gráfica:', error);
            container.innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Error al cargar el archivo: ${error.message}
                </div>
            `;
        }
    }

    /**
     * Analiza la estructura de datos para determinar tipo de gráfica y extraer componentes
     */
    parseDataStructure(dataMatrix) {
        let tipoGrafica = null;
        let CabeceraY = null;

        if (!dataMatrix[0][0] || dataMatrix[0][0].trim() === "") {
            tipoGrafica = "A";
            CabeceraY = dataMatrix[1][0];
        } else {
            tipoGrafica = "B";
            CabeceraY = dataMatrix[0][0];
        }

        let RowsY = [];
        if (tipoGrafica === "A") {
            RowsY = dataMatrix.slice(2).map(row => row[0]);
        } else {
            RowsY = dataMatrix.slice(1).map(row => row[0]);
        }

        let GroupColsX = [];
        let ColsX = [];

        if (tipoGrafica === "A") {
            const groupRow = dataMatrix[0].slice(1);
            const colHeaders = dataMatrix[1].slice(1);

            let currentGroup = null;
            let currentCols = [];
            for (let i = 0; i < groupRow.length; i++) {
                if (groupRow[i] && groupRow[i].trim() !== "") {
                    if (currentGroup) {
                        GroupColsX.push({ group: currentGroup, cols: currentCols });
                    }
                    currentGroup = groupRow[i];
                    currentCols = [colHeaders[i]];
                } else if (currentGroup) {
                    currentCols.push(colHeaders[i]);
                }
            }
            if (currentGroup) {
                GroupColsX.push({ group: currentGroup, cols: currentCols });
            }
            ColsX = colHeaders;
        } else {
            ColsX = dataMatrix[0].slice(1);
            GroupColsX = ColsX.map(col => ({ group: col, cols: [col] }));
        }

        return { tipoGrafica, CabeceraY, RowsY, GroupColsX, ColsX };
    }

    /**
     * Carga SheetJS dinámicamente
     */
    async loadSheetJS() {
        if (typeof XLSX !== 'undefined') return;
        return new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js';
            script.onload = resolve;
            script.onerror = reject;
            document.head.appendChild(script);
        });
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
     * Extrae y limpia la matriz de datos
     */
    extractAndCleanData(worksheet, range) {
        const dataMatrix = [];
        const headerRow = [];

        for (let r = range.s.r; r <= range.e.r; r++) {
            const row = [];
            for (let c = range.s.c; c <= range.e.c; c++) {
                const ref = XLSX.utils.encode_cell({ r, c });
                const cell = worksheet[ref];
                let value = cell?.v !== undefined && cell.v !== null ? cell.v.toString() : '';
                if (r === range.s.r) headerRow.push(value);
                row.push(value);
            }
            dataMatrix.push(row);
        }

        return dataMatrix.filter(row => row.some(cell => cell && cell.trim().length > 0));
    }

    /**
     * Renderiza la interfaz de selección de parámetros
     */
    renderSelectionInterface(container, dataMatrix, fileName, excelUrl, { tipoGrafica, CabeceraY, RowsY, GroupColsX, ColsX }) {
        if (!document.getElementById('grafica-modal-zindex-style')) {
            const style = document.createElement('style');
            style.id = 'grafica-modal-zindex-style';
            style.innerHTML = `
                .grafica-modal-checkbox-list {
                    position: relative;
                    display: flex;
                    flex-direction: column;
                    background: rgba(161, 230, 207, 1)!important;
                    gap: 0.4rem;
                    width: 100%;
                    max-height: 20vh;
                    overflow-y: auto;
                    overflow-x: hidden;
                    padding: 0.5rem;
                    border-radius: 6px;
                    box-sizing: border-box;
                }

                .grafica-control-row {
                    display: flex;
                    gap: 0.75rem;
                    align-items: flex-start;
                    flex-wrap: wrap;
                }
                .grafica-control-row .col-12.col-md-6 {
                    flex: 1 1 48%;
                    max-width: 48%;
                    min-width: 0;
                }
                @media (max-width: 767.98px) {
                    .grafica-control-row .col-12.col-md-6 {
                        flex-basis: 100%;
                        max-width: 100%;
                    }
                }

                .grafica-toggle-btn {
                    background: rgba(93, 170, 144, 1)!important;
                    border-left: 4px solid #535353ff !important;
                    padding: 0.25rem 0.5rem;
                    width: 100%;
                    text-align: left;
                    transition: background-color 0.18s ease, box-shadow 0.12s ease, transform 0.06s ease;
                    border-radius: 6px;
                    display: flex;
                    align-items: center;
                    gap: 0.5rem;
                }
                .grafica-toggle-btn .form-label { flex: 1; }
                .grafica-toggle-btn:hover {
                    background: rgba(133, 133, 133, 1)!important;
                    cursor: pointer;
                    box-shadow: 0 1px 6px rgba(0,0,0,0.06);
                    border-left: 4px solid #0a0909ff !important;
                }
                .grafica-toggle-btn.saved {
                    background: #abe4bcff !important;
                    color: #155724 !important;
                    border-left: 4px solid #28a745 !important;
                }
                .grafica-toggle-btn.pressed {
                    background: #53c074ff !important;
                    color: #062e10ff !important;
                    border-left: 4px solid #053a11ff !important;
                    transform: translateY(2px);
                }
            `;
            document.head.appendChild(style);
        }

        const selectionHTML = `
            <div class="row g-2 align-items-start mb-2 grafica-control-row">
                <div class="col-12 col-md-6">
                    <button id="toggleRowsY" type="button" class="grafica-toggle-btn btn p-0 d-flex justify-content-between align-items-center mb-1 w-100 text-start" style="background:none;border:0;" aria-expanded="true" aria-controls="rowsYCheckboxes">
                        <div class="form-label mb-0 text-center"><small><b>${CabeceraY}:</b></small></div>
                        <div id="toggleRowsYIcon" style="font-size:1.5em;" aria-hidden="true"></div>
                    </button>
                    <div id="rowsYCheckboxes" class="grafica-modal-checkbox-list"></div>
                </div>
                <div class="col-12 col-md-6">
                    <button id="toggleColsX" type="button" class="grafica-toggle-btn btn p-0 d-flex justify-content-between align-items-center mb-1 w-100 text-start" style="background:none;border:0;" aria-expanded="true" aria-controls="groupedColumnCheckboxes">
                        <div class="form-label mb-0 text-center"><small><b>Columnas/grupos:</b></small></div>
                        <div id="toggleColsXIcon" style="font-size:1.5em;" aria-hidden="true"></div>
                    </button>
                    <div id="groupedColumnCheckboxes" class="grafica-modal-checkbox-list"></div>
                </div>
            </div>
            <div class="row g-2 mt-2 mb-2">
                <div class="col-12 col-md-6">
                    <label class="form-label mb-1"><small><b>Tipo de gráfica:</b></small></label>
                    <select id="chartType" class="form-select form-select-sm">
                        <option value="bar">Barra</option>
                        <option value="line">Línea</option>
                        <option value="area">Área</option>
                        <option value="radar">Radar</option>
                        <option value="polarArea">Polar</option>
                        <option value="doughnut">Dona</option>
                        <option value="pie">Pastel</option>
                    </select>
                </div>
                <div class="col-12 col-md-6 d-flex align-items-end">
                    <button id="renderChartBtn" class="btn btn-outline-primary btn-sm w-100">Actualizar Gráfica</button>
                </div>
            </div>
            <div class="alert alert-danger mt-3 mb-1 py-3 px-2">
                <i class="bi bi-info-circle me-1"></i>El motor que genera la gráfica automáticamente activa todas las opciones, para ajustar la visualización de los datos disponibles:<br>
                <i class="bi bi-info-circle me-1"></i>Hay botones en la parte superior de la vista que logran que la gráfica se actualice automáticamente al cambiar las selecciones y se ocultan al hacer click en el boton pertinente. <br>
                <b><i class="bi bi-info-circle me-1"></i>Nota:</b> No todas las gráficas son viables para la visualización de cada estadística, seleccione el tipo de gráfica más adecuada. El tipo de gráfica de Barra es adecuada universalmente.
            </div>
            <div id="chartContainer" class="mb-3"></div>
        `;
        container.innerHTML = selectionHTML;

        const collapseState = { rowsY: true, colsX: true };

        function setCollapsed(which, collapsed) {
            collapseState[which] = !!collapsed;
        }

        function isCollapsed(which) {
            return collapseState[which] === true;
        }

        // Función para actualizar el estado visual del botón
        function updateToggle(buttonId, iconId, targetDiv, collapsed) {
            const btn = document.getElementById(buttonId);
            const icon = document.getElementById(iconId);
            const div = document.getElementById(targetDiv);

            if (collapsed) {
                div.style.display = 'none';
                if (icon) icon.innerHTML = '<i class="bi bi-arrows-expand"></i>';
                btn.setAttribute('aria-expanded', 'false');
                btn.classList.remove('saved');
                btn.title = `Expandir ${targetDiv === 'rowsYCheckboxes' ? CabeceraY : 'columnas y grupos'}. Haz clic para ver las opciones.`;
            } else {
                div.style.display = '';
                if (icon) icon.innerHTML = '<i class="bi bi-arrows-collapse"></i>';
                btn.setAttribute('aria-expanded', 'true');
                btn.classList.add('saved');
                btn.title = `Colapsar ${targetDiv === 'rowsYCheckboxes' ? CabeceraY : 'columnas y grupos'}. Haz clic para ocultar las opciones.`;
            }
        }

        // Toggle para RowsY
        const btnRowsY = document.getElementById('toggleRowsY');
        btnRowsY.addEventListener('click', () => {
            btnRowsY.classList.add('pressed');
            setTimeout(() => btnRowsY.classList.remove('pressed'), 220);
            setCollapsed('rowsY', !isCollapsed('rowsY'));
            updateToggle('toggleRowsY', 'toggleRowsYIcon', 'rowsYCheckboxes', isCollapsed('rowsY'));
        });

        // Toggle para ColsX
        const btnColsX = document.getElementById('toggleColsX');
        btnColsX.addEventListener('click', () => {
            btnColsX.classList.add('pressed');
            setTimeout(() => btnColsX.classList.remove('pressed'), 220);
            setCollapsed('colsX', !isCollapsed('colsX'));
            updateToggle('toggleColsX', 'toggleColsXIcon', 'groupedColumnCheckboxes', isCollapsed('colsX'));
        });

        // Eventos para el estado 'pressed'
        ['mousedown','mouseup','mouseleave','touchstart','touchend'].forEach(ev => {
            if (ev === 'mousedown' || ev === 'touchstart') {
                btnRowsY.addEventListener(ev, () => btnRowsY.classList.add('pressed'));
                btnColsX.addEventListener(ev, () => btnColsX.classList.add('pressed'));
            } else {
                btnRowsY.addEventListener(ev, () => btnRowsY.classList.remove('pressed'));
                btnColsX.addEventListener(ev, () => btnColsX.classList.remove('pressed'));
            }
        });

        // Inicializar estados
        updateToggle('toggleRowsY', 'toggleRowsYIcon', 'rowsYCheckboxes', isCollapsed('rowsY'));
        updateToggle('toggleColsX', 'toggleColsXIcon', 'groupedColumnCheckboxes', isCollapsed('colsX'));

        // Renderizar checkboxes para RowsY
        this.renderRowsYCheckboxes(document.getElementById('rowsYCheckboxes'), RowsY);

        // Renderizar checkboxes jerárquicos para columnas
        this.renderGroupedColumnCheckboxes(document.getElementById('groupedColumnCheckboxes'), GroupColsX);

        // Lógica de actualización de gráfica
        const updateChart = () => {
            const selectedRowIndices = Array.from(document.querySelectorAll('.rowy-checkbox:checked'))
                .map(cb => parseInt(cb.value, 10));

            const selectedColumnCheckboxes = Array.from(document.querySelectorAll('.column-checkbox:checked'));
            const selectedColNames = selectedColumnCheckboxes.map(cb => cb.value);
            const selectedColLabels = selectedColumnCheckboxes.map(cb => cb.getAttribute('data-full-label') || cb.value);

            if (selectedRowIndices.length === 0 || selectedColNames.length === 0) {
                const chartContainer = document.getElementById('chartContainer');
                if (this.chartInstance) {
                    this.chartInstance.destroy();
                    this.chartInstance = null;
                }
                chartContainer.innerHTML = '<div class="alert alert-info text-center"><i class="bi bi-info-circle me-1"></i>Selecciona filas y columnas para ver la gráfica</div>';
                return;
            }

            let colIndices = [];
            if (tipoGrafica === "A") {
                let lastPos = 0;
                selectedColNames.forEach(colName => {
                    const headers = dataMatrix[1];
                    let found = -1;
                    for (let i = lastPos; i < headers.length; i++) {
                        if (headers[i] === colName) {
                            found = i;
                            lastPos = i + 1;
                            break;
                        }
                    }
                    if (found !== -1) colIndices.push(found);
                });
            } else {
                let lastPos = 0;
                selectedColNames.forEach(colName => {
                    const headers = dataMatrix[0];
                    let found = -1;
                    for (let i = lastPos; i < headers.length; i++) {
                        if (headers[i] === colName) {
                            found = i;
                            lastPos = i + 1;
                            break;
                        }
                    }
                    if (found !== -1) colIndices.push(found);
                });
            }

            const chartTypeSelect = document.getElementById('chartType');
            const type = chartTypeSelect ? chartTypeSelect.value : 'bar';

            const chartContainer = document.getElementById('chartContainer');
            this.renderChartHierarchical(chartContainer, dataMatrix, selectedRowIndices, colIndices, type, tipoGrafica, selectedColLabels);
        };

        // Event listeners
        document.getElementById('rowy-select-all').addEventListener('change', e => {
            const checked = e.target.checked;
            document.querySelectorAll('.rowy-checkbox').forEach(cb => cb.checked = checked);
            updateChart();
        });

        document.querySelectorAll('.rowy-checkbox').forEach(cb => {
            cb.addEventListener('change', () => {
                const allChecked = Array.from(document.querySelectorAll('.rowy-checkbox')).every(cb2 => cb2.checked);
                document.getElementById('rowy-select-all').checked = allChecked;
                updateChart();
            });
        });

        document.querySelectorAll('.group-checkbox').forEach((groupCb, gIdx) => {
            groupCb.addEventListener('change', () => {
                const checked = groupCb.checked;
                document.querySelectorAll(`.column-checkbox.group-${gIdx}`).forEach(cb => cb.checked = checked);
                updateChart();
            });
        });

        document.querySelectorAll('.column-checkbox').forEach(cb => {
            cb.addEventListener('change', () => {
                const gIdx = cb.classList[cb.classList.length - 1].split('-')[1];
                const allChecked = Array.from(document.querySelectorAll(`.column-checkbox.group-${gIdx}`)).every(cb2 => cb2.checked);
                document.getElementById(`group-${gIdx}`).checked = allChecked;
                updateChart();
            });
        });

        document.getElementById('chartType').addEventListener('change', updateChart);
        document.getElementById('renderChartBtn').addEventListener('click', updateChart);

        // Cargar Chart.js
        this.loadChartJS().then(() => {
            setTimeout(updateChart, 200);
        }).catch(error => {
            console.error('Error cargando Chart.js:', error);
            const chartContainer = document.getElementById('chartContainer');
            chartContainer.innerHTML = '<div class="alert alert-danger">Error cargando Chart.js. <a href="https://cdn.jsdelivr.net/npm/chart.js" target="_blank">Cargar manualmente</a></div>';
        });
    }

    /**
     * Renderiza los checkboxes para RowsY
     */
    renderRowsYCheckboxes(container, RowsY) {
        const isLongText = (txt) => txt && txt.length > 18;
        const rowBlocks = [];
        let tempBlock = [];
        let blockSize = 4;

        for (let i = 0; i < RowsY.length; i++) {
            const txt = RowsY[i] || '';
            blockSize = isLongText(txt) ? 2 : 4;
            tempBlock.push({ txt, idx: i });
            if (tempBlock.length === blockSize || i === RowsY.length - 1) {
                rowBlocks.push({ block: [...tempBlock], blockSize });
                tempBlock = [];
            }
        }

        container.innerHTML = `
            <div class="d-grid gap-2 col-12 mx-auto">
                <input type="checkbox" class="btn-check" id="rowy-select-all" autocomplete="off" checked>
                <label class="btn btn-outline-success mb-2 p-2 fw-bold" for="rowy-select-all">(Seleccionar/Deseleccionar todo)</label>
            </div>
        `;

        rowBlocks.forEach(({ block, blockSize }) => {
            container.innerHTML += `
                <div class="row">
                    ${block.map(({ txt, idx }) => `
                        <div class="${blockSize === 2 ? 'col-6' : 'col-3'} px-1">
                            <input type="checkbox" class="btn-check rowy-checkbox" id="rowy-${idx}" value="${idx}" autocomplete="off" checked>
                            <label class="btn btn-outline-success mb-2 p-2 w-100 text-truncate" style="white-space:normal;" for="rowy-${idx}" title="${txt}">${txt}</label>
                        </div>
                    `).join('')}
                </div>
            `;
        });
    }

    /**
     * Renderiza los checkboxes jerárquicos para columnas
     */
    renderGroupedColumnCheckboxes(container, GroupColsX) {
        container.innerHTML = GroupColsX.map((group, gIdx) => {
            const onlyOneAndEqual = group.cols.length === 1 && group.group === group.cols[0];
            if (onlyOneAndEqual) {
                // Solo imprime el hijo (columna), no el grupo padre
                return `
                    <div class="mb-2 border rounded p-2">
                        <div class="ms-3">
                            <div>
                                <input type="checkbox" class="form-check-input column-checkbox group-${gIdx}" 
                                       id="col-${gIdx}-0" 
                                       value="${group.cols[0]}" 
                                       data-group="${group.group}"
                                       data-full-label="${group.cols[0]}"
                                       checked>
                                <label class="form-check-label" for="col-${gIdx}-0">${group.cols[0]}</label>
                            </div>
                        </div>
                    </div>
                `;
            }
            // Caso normal: imprime grupo padre y sus hijos (excepto cuando hijo == padre)
            return `
                <div class="mb-2 border rounded p-2">
                    <div>
                        <input type="checkbox" class="form-check-input group-checkbox" id="group-${gIdx}">
                        <label class="form-check-label fw-bold" for="group-${gIdx}">${group.group}</label>
                    </div>
                    <div class="ms-3">
                        ${group.cols.map((col, cIdx) => {
                            if (group.group === col) {
                                return '';
                            } else {
                                return `
                                    <div>
                                        <input type="checkbox" class="form-check-input column-checkbox group-${gIdx}" 
                                               id="col-${gIdx}-${cIdx}" 
                                               value="${col}" 
                                               data-group="${group.group}"
                                               data-full-label="${col}"
                                               checked>
                                        <label class="form-check-label" for="col-${gIdx}-${cIdx}">${col}</label>
                                    </div>
                                `;
                            }
                        }).join('')}
                    </div>
                </div>
            `;
        }).join('');
    }

    /**
     * Carga Chart.js dinámicamente
     */
    async loadChartJS() {
        if (typeof Chart !== 'undefined') return Promise.resolve();
        return new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
            script.onload = resolve;
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }

    /**
     * Genera la gráfica con Chart.js usando selección múltiple de filas y columnas
     */
    renderChartHierarchical(container, dataMatrix, selectedRowIndices, yColIndices, type, tipoGrafica, colLabels = null) {
        if (this.chartInstance) {
            this.chartInstance.destroy();
            this.chartInstance = null;
        }

        if (!selectedRowIndices || selectedRowIndices.length === 0 || !yColIndices || yColIndices.length === 0) {
            container.innerHTML = '<div class="alert alert-warning text-center">No hay datos suficientes para mostrar la gráfica</div>';
            return;
        }

        let labels = [];
        if (tipoGrafica === "A") {
            labels = selectedRowIndices.map(idx => dataMatrix[idx + 2] ? dataMatrix[idx + 2][0] : '');
        } else {
            labels = selectedRowIndices.map(idx => dataMatrix[idx + 1] ? dataMatrix[idx + 1][0] : '');
        }

        const datasets = yColIndices.map((colIdx, i) => {
            const label = colLabels && colLabels[i] ? colLabels[i] : (tipoGrafica === "A" ? dataMatrix[1][colIdx] : dataMatrix[0][colIdx]);
            let data = selectedRowIndices.map(rowIdx => {
                let value = 0;
                let actualRowIndex;
                if (tipoGrafica === "A") {
                    actualRowIndex = rowIdx + 2;
                } else {
                    actualRowIndex = rowIdx + 1;
                }
                if (dataMatrix[actualRowIndex] && dataMatrix[actualRowIndex][colIdx] !== undefined) {
                    value = parseFloat(dataMatrix[actualRowIndex][colIdx]) || 0;
                }
                return value;
            });

            if (type === 'scatter') {
                data = data.map((y, idx) => ({ x: labels[idx], y }));
            }

            let fill = false;
            if (type === 'area') fill = true;

            return {
                label: label,
                data: data,
                backgroundColor: this.getColor(i),
                borderColor: this.getColor(i),
                borderWidth: 2,
                fill: fill
            };
        });

        const canvas = document.createElement('canvas');
        canvas.id = 'dynamicChart';
        canvas.height = 400;
        container.innerHTML = '';
        container.appendChild(canvas);

        const ctx = canvas.getContext('2d');

        let chartType = type;
        if (type === 'area') chartType = 'line';
        if (type === 'horizontalBar') chartType = 'bar';

        const chartData = { labels, datasets };
        const options = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: true, position: 'top' },
                tooltip: { enabled: true, intersect: false, mode: 'index' }
            },
            scales: (['bar','line','area','horizontalBar','scatter'].includes(type)) ? {
                x: type === 'horizontalBar' ? { beginAtZero: true, type: 'category' } : {},
                y: { beginAtZero: true }
            } : {}
        };

        if (type === 'horizontalBar') {
            options.indexAxis = 'y';
        }

        try {
            this.chartInstance = new Chart(ctx, {
                type: chartType,
                data: chartData,
                options: options
            });
        } catch (error) {
            console.error('Error creando gráfica:', error);
            container.innerHTML = '<div class="alert alert-danger">Error al crear la gráfica. Verifica que Chart.js esté cargado.</div>';
        }
    }

    /**
     * Obtiene color para dataset
     */
    getColor(index) {
        const colors = [
            'rgba(54, 162, 235, 0.7)',
            'rgba(255, 99, 132, 0.7)',
            'rgba(255, 206, 86, 0.7)',
            'rgba(75, 192, 192, 0.7)',
            'rgba(153, 102, 255, 0.7)',
            'rgba(255, 159, 64, 0.7)',
            'rgba(199, 199, 199, 0.7)',
            'rgba(83, 102, 255, 0.7)',
            'rgba(255, 102, 178, 0.7)',
            'rgba(102, 255, 178, 0.7)',
            'rgba(255, 178, 102, 0.7)',
            'rgba(178, 102, 255, 0.7)',
            'rgba(102, 178, 255, 0.7)',
            'rgba(255, 102, 102, 0.7)',
            'rgba(102, 255, 102, 0.7)',
            'rgba(255, 204, 153, 0.7)',
            'rgba(204, 255, 153, 0.7)',
            'rgba(153, 255, 204, 0.7)',
            'rgba(153, 204, 255, 0.7)',
            'rgba(255, 153, 204, 0.7)',
            'rgba(220, 180, 120, 0.7)',
            'rgba(120, 200, 180, 0.7)',
            'rgba(180, 120, 200, 0.7)',
            'rgba(200, 120, 120, 0.7)'
        ];
        return colors[index % colors.length];
    }
}

// Instancia global
window.GraficaModalEngine = new GraficaModalEngine();