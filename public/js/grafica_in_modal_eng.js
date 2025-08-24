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

            // Detectar tipo de gráfica y extraer estructuras
            const { tipoGrafica, CabeceraY, RowsY, GroupColsX, ColsX } = this.parseDataStructure(dataMatrix);

            // Renderizar interfaz de selección
            this.renderSelectionInterface(container, dataMatrix, fileName, excelUrl, tipoGrafica, CabeceraY, RowsY, GroupColsX);
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
     * Parsea la estructura de datos para obtener tipo, etiquetas, filas y columnas
     */
    parseDataStructure(dataMatrix) {
        let tipoGrafica = "B";
        let CabeceraY = dataMatrix[0][0] || "";
        let RowsY = [];
        let GroupColsX = [];
        let ColsX = [];

        if (!dataMatrix[0][0] || dataMatrix[0][0].trim() === "") {
            tipoGrafica = "A";
            CabeceraY = dataMatrix[1][0];
            RowsY = dataMatrix.slice(2).map(row => row[0]);
            const groupRow = dataMatrix[0].slice(1);
            const colHeaders = dataMatrix[1].slice(1);

            let currentGroup = null;
            let currentCols = [];
            for (let i = 0; i < groupRow.length; i++) {
                if (groupRow[i] && groupRow[i].trim()) {
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
            RowsY = dataMatrix.slice(1).map(row => row[0]);
            ColsX = dataMatrix[0].slice(1);
            GroupColsX = ColsX.map(col => ({ group: col, cols: [col] }));
        }

        return { tipoGrafica, CabeceraY, RowsY, GroupColsX, ColsX };
    }

    /**
     * Renderiza la interfaz de selección con acordion y checkboxes jerárquicos
     */
    renderSelectionInterface(container, dataMatrix, fileName, excelUrl, tipoGrafica, CabeceraY, RowsY, GroupColsX) {
        const selectionHTML = `
            <div class="accordion mb-3" id="graficaAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="graficaAccordionHeading">
                        <button class="accordion-button py-2 px-3" type="button" data-bs-toggle="collapse" data-bs-target="#graficaAccordionCollapse" aria-expanded="true" aria-controls="graficaAccordionCollapse">
                            <strong>Opciones de visualización</strong>
                        </button>
                    </h2>
                    <div id="graficaAccordionCollapse" class="accordion-collapse collapse show" aria-labelledby="graficaAccordionHeading" data-bs-parent="#graficaAccordion">
                        <div class="accordion-body py-2 px-3">
                            <div class="row g-2 align-items-start">
                                <div class="col-12 col-md-6">
                                    <label class="form-label mb-1"><small><b>${CabeceraY}:</b></small></label>
                                    <div id="rowsYCheckboxes" class="small"></div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label mb-1"><small><b>Columnas/grupos:</b></small></label>
                                    <div id="groupedColumnCheckboxes" class="small"></div>
                                </div>
                            </div>
                            <div class="row g-2 mt-2">
                                <div class="col-12 col-md-6">
                                    <label class="form-label mb-1"><small><b>Tipo de gráfica:</b></small></label>
                                    <select id="chartType" class="form-select form-select-sm">
                                        <option value="bar">Barra vertical</option>
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
                            <div class="alert alert-info mt-2 mb-0 py-1 px-2 small">
                                <i class="bi bi-info-circle me-1"></i>La gráfica se actualiza automáticamente al cambiar las selecciones
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="chartContainer" class="mb-3"></div>
        `;
        container.innerHTML = selectionHTML;

        // Renderizar checkboxes de filas
        this.renderRowsYCheckboxes(RowsY);
        // Renderizar checkboxes jerárquicos de columnas
        this.renderGroupedColumnCheckboxes(GroupColsX);

        // Configurar eventos
        this.setupEventListeners(dataMatrix, tipoGrafica, container);

        // Cargar Chart.js y generar gráfica inicial
        this.loadChartJS().then(() => {
            setTimeout(() => this.updateChart(), 200);
        }).catch(error => {
            console.error('Error cargando Chart.js:', error);
            const chartContainer = document.getElementById('chartContainer');
            chartContainer.innerHTML = '<div class="alert alert-danger">Error cargando Chart.js.</div>';
        });
    }

    /**
     * Renderiza checkboxes para filas Y
     */
    renderRowsYCheckboxes(RowsY) {
        const container = document.getElementById('rowsYCheckboxes');
        container.innerHTML = `
            <div>
                <input type="checkbox" class="form-check-input" id="rowy-select-all" checked>
                <label class="form-check-label fw-bold" for="rowy-select-all">(Seleccionar/Deseleccionar todo)</label>
            </div>
        `;
        RowsY.forEach((row, idx) => {
            container.innerHTML += `
                <div>
                    <input type="checkbox" class="form-check-input rowy-checkbox" id="rowy-${idx}" value="${idx}" checked>
                    <label class="form-check-label" for="rowy-${idx}">${row}</label>
                </div>
            `;
        });
    }

    /**
     * Renderiza checkboxes jerárquicos para columnas
     */
    renderGroupedColumnCheckboxes(GroupColsX) {
        const container = document.getElementById('groupedColumnCheckboxes');
        container.innerHTML = GroupColsX.map((group, gIdx) => `
            <div class="mb-2 border rounded p-2">
                <div>
                    <input type="checkbox" class="form-check-input group-checkbox" id="group-${gIdx}" checked>
                    <label class="form-check-label fw-bold" for="group-${gIdx}">${group.group}</label>
                </div>
                <div class="ms-3">
                    ${group.cols.map((col, cIdx) => {
                        const fullLabel = (group.group === col) ? group.group : `${group.group} - ${col}`;
                        return `
                            <div>
                                <input type="checkbox" class="form-check-input column-checkbox group-${gIdx}" 
                                       id="col-${gIdx}-${cIdx}" 
                                       value="${col}" 
                                       data-group="${group.group}"
                                       data-full-label="${fullLabel}"
                                       checked>
                                <label class="form-check-label" for="col-${gIdx}-${cIdx}">${fullLabel}</label>
                            </div>
                        `;
                    }).join('')}
                </div>
            </div>
        `).join('');

        // Actualizar estados de padres después de renderizar
        this.syncGroupCheckboxes();
    }

    /**
     * Sincroniza el estado de los checkboxes de grupo según sus hijos
     */
    syncGroupCheckboxes() {
        document.querySelectorAll('.group-checkbox').forEach(groupCb => {
            const groupId = groupCb.id.split('-')[1];
            const allChildren = document.querySelectorAll(`.column-checkbox.group-${groupId}`);
            const allChecked = Array.from(allChildren).every(cb => cb.checked);
            groupCb.checked = allChecked;
        });
    }

    /**
     * Configura todos los listeners
     */
    setupEventListeners(dataMatrix, tipoGrafica, container) {
        // Select All para filas
        const selectAllRowsY = document.getElementById('rowy-select-all');
        selectAllRowsY.addEventListener('change', () => {
            const checked = selectAllRowsY.checked;
            document.querySelectorAll('.rowy-checkbox').forEach(cb => cb.checked = checked);
            this.updateChart();
        });

        // Checkboxes de filas individuales
        document.querySelectorAll('.rowy-checkbox').forEach(cb => {
            cb.addEventListener('change', () => {
                const allChecked = Array.from(document.querySelectorAll('.rowy-checkbox')).every(cb2 => cb2.checked);
                selectAllRowsY.checked = allChecked;
                this.updateChart();
            });
        });

        // Checkboxes de grupo (padre)
        document.querySelectorAll('.group-checkbox').forEach(groupCb => {
            groupCb.addEventListener('change', () => {
                const groupId = groupCb.id.split('-')[1];
                const children = document.querySelectorAll(`.column-checkbox.group-${groupId}`);
                children.forEach(cb => cb.checked = groupCb.checked);
                this.updateChart();
            });
        });

        // Checkboxes de columna (hijo)
        document.querySelectorAll('.column-checkbox').forEach(cb => {
            cb.addEventListener('change', () => {
                const groupId = cb.classList.value.split(' ')[1].split('-')[1];
                const groupCb = document.getElementById(`group-${groupId}`);
                const allChecked = Array.from(document.querySelectorAll(`.column-checkbox.group-${groupId}`)).every(cb2 => cb2.checked);
                groupCb.checked = allChecked;
                this.updateChart();
            });
        });

        // Tipo de gráfica
        const chartTypeSelect = document.getElementById('chartType');
        chartTypeSelect.addEventListener('change', () => this.updateChart());

        // Botón actualizar
        const renderBtn = document.getElementById('renderChartBtn');
        renderBtn.addEventListener('click', () => this.updateChart());
    }

    /**
     * Actualiza la gráfica basada en selecciones actuales
     */
    updateChart() {
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

        // Mapear nombres de columnas a índices
        const headers = this.getHeadersFromDataMatrix(this.dataMatrix, this.tipoGrafica);
        const colIndices = selectedColNames.map(name => headers.indexOf(name));
        const validColIndices = colIndices.filter(idx => idx !== -1);

        if (validColIndices.length === 0) return;

        const chartType = document.getElementById('chartType').value;
        const chartContainer = document.getElementById('chartContainer');

        this.renderChartHierarchical(chartContainer, this.dataMatrix, selectedRowIndices, validColIndices, chartType, this.tipoGrafica, selectedColLabels);
    }

    /**
     * Obtiene las cabeceras de columnas según el tipo de gráfica
     */
    getHeadersFromDataMatrix(dataMatrix, tipoGrafica) {
        return tipoGrafica === 'A' ? dataMatrix[1] : dataMatrix[0];
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
        for (let r = range.s.r; r <= range.e.r; r++) {
            const row = [];
            for (let c = range.s.c; c <= range.e.c; c++) {
                const ref = XLSX.utils.encode_cell({ r, c });
                const cell = worksheet[ref];
                let value = cell && cell.v !== undefined && cell.v !== null ? cell.v.toString() : '';
                row.push(value);
            }
            dataMatrix.push(row);
        }
        return dataMatrix.filter(row => row.some(cell => cell && cell.trim().length > 0));
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
     * Genera la gráfica con Chart.js
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

        // Etiquetas eje X
        let labels = [];
        if (tipoGrafica === "A") {
            labels = selectedRowIndices.map(idx => dataMatrix[idx + 2] ? dataMatrix[idx + 2][0] : '');
        } else {
            labels = selectedRowIndices.map(idx => dataMatrix[idx + 1] ? dataMatrix[idx + 1][0] : '');
        }

        // Datasets
        const datasets = yColIndices.map((colIdx, i) => {
            const label = colLabels && colLabels[i] ? colLabels[i] : (tipoGrafica === "A" ? dataMatrix[1][colIdx] : dataMatrix[0][colIdx]);
            const data = selectedRowIndices.map(rowIdx => {
                let value = 0;
                let actualRowIndex = tipoGrafica === "A" ? rowIdx + 2 : rowIdx + 1;
                if (dataMatrix[actualRowIndex] && dataMatrix[actualRowIndex][colIdx] !== undefined) {
                    value = parseFloat(dataMatrix[actualRowIndex][colIdx]) || 0;
                }
                return value;
            });

            const fill = type === 'area';
            return {
                label,
                data,
                backgroundColor: this.getColor(i),
                borderColor: this.getColor(i),
                borderWidth: 2,
                fill
            };
        });

        const canvas = document.createElement('canvas');
        canvas.id = 'dynamicChart';
        canvas.height = 400;
        container.innerHTML = '';
        container.appendChild(canvas);

        const ctx = canvas.getContext('2d');
        const chartType = type === 'area' ? 'line' : type;
        const options = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: true, position: 'top' },
                tooltip: { enabled: true, intersect: false, mode: 'index' }
            },
            scales: {
                x: {},
                y: { beginAtZero: true }
            }
        };

        if (type === 'horizontalBar') {
            options.indexAxis = 'y';
            options.scales.x.beginAtZero = true;
        }

        try {
            this.chartInstance = new Chart(ctx, {
                type: chartType,
                data: { labels, datasets },
                options
            });
        } catch (error) {
            console.error('Error creando gráfica:', error);
            container.innerHTML = '<div class="alert alert-danger">Error al crear la gráfica.</div>';
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
            'rgba(255, 159, 64, 0.7)'
        ];
        return colors[index % colors.length];
    }
}

// Instancia global
window.GraficaModalEngine = new GraficaModalEngine();