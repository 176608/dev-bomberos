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
            // Cargar SheetJS si no está presente
            await this.loadSheetJS();

            // Obtener datos del Excel
            const arrayBuffer = await this.fetchExcelFile(excelUrl);
            const workbook = XLSX.read(arrayBuffer, { type: 'array', cellStyles: true });
            const worksheet = workbook.Sheets[workbook.SheetNames[0]];
            const range = XLSX.utils.decode_range(worksheet['!ref'] || 'A1:A1');

            // Extraer y limpiar la matriz
            const dataMatrix = this.extractAndCleanData(worksheet, range);

            console.log('📊 Matriz limpia:', dataMatrix);

            // 1) Detectar tipo de gráfica y CabeceraY
            let tipoGrafica = null;
            let CabeceraY = null;

            if (!dataMatrix[0][0] || dataMatrix[0][0].trim() === "") {
                // Gráfica Tipo A
                tipoGrafica = "A";
                CabeceraY = dataMatrix[1][0];
            } else {
                // Gráfica Tipo B
                tipoGrafica = "B";
                CabeceraY = dataMatrix[0][0];
            }

            // 2) RowsY
            let RowsY = [];
            if (tipoGrafica === "A") {
                // Desde dataMatrix[2] en adelante, columna 0
                RowsY = dataMatrix.slice(2).map(row => row[0]);
            } else {
                // Desde dataMatrix[1] en adelante, columna 0
                RowsY = dataMatrix.slice(1).map(row => row[0]);
            }

            // 3) GroupColsX y ColsX
            let GroupColsX = [];
            let ColsX = [];

            if (tipoGrafica === "A") {
                // GroupColsX: Agrupar por dataMatrix[0], ignorando el primer elemento (col 0)
                const groupRow = dataMatrix[0].slice(1);
                const colHeaders = dataMatrix[1].slice(1);

                let currentGroup = null;
                let currentCols = [];
                for (let i = 0; i < groupRow.length; i++) {
                    if (groupRow[i] && groupRow[i].trim() !== "") {
                        // Nuevo grupo
                        if (currentGroup) {
                            GroupColsX.push({ group: currentGroup, cols: currentCols });
                        }
                        currentGroup = groupRow[i];
                        currentCols = [colHeaders[i]];
                    } else if (currentGroup) {
                        // Columna vacía, pertenece al grupo actual
                        currentCols.push(colHeaders[i]);
                    }
                }
                // Push último grupo
                if (currentGroup) {
                    GroupColsX.push({ group: currentGroup, cols: currentCols });
                }

                // ColsX: todos los headers de la fila 1, excepto el primero
                ColsX = colHeaders;
            } else {
                // Tipo B: ColsX = todos los elementos de dataMatrix[0] excepto el primero
                ColsX = dataMatrix[0].slice(1);
                // GroupColsX no aplica o es igual a ColsX
                GroupColsX = ColsX.map(col => ({ group: col, cols: [col] }));
            }

            // Ejemplo de salida:
            console.log("Tipo de gráfica:", tipoGrafica);
            console.log("CabeceraY:", CabeceraY);
            console.log("RowsY:", RowsY);
            console.log("GroupColsX:", GroupColsX);
            console.log("ColsX:", ColsX);

            // Mostrar interfaz de selección
            this.renderSelectionInterface(container, dataMatrix, fileName, excelUrl);

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

        // Recorrer todas las celdas
        for (let r = range.s.r; r <= range.e.r; r++) {
            const row = [];
            for (let c = range.s.c; c <= range.e.c; c++) {
                const ref = XLSX.utils.encode_cell({ r, c });
                const cell = worksheet[ref];

                let value = '';
                if (cell && cell.v !== undefined && cell.v !== null) {
                    value = cell.v.toString();
                }

                if (r === range.s.r) {
                    headerRow.push(value);
                }
                row.push(value);
            }
            dataMatrix.push(row);
        }

        // Purgar filas vacías
        const cleanedMatrix = dataMatrix.filter(row => {
            return row.some(cell => cell && cell.trim().length > 0);
        });

        return cleanedMatrix;
    }

    /**
     * Renderiza la interfaz de selección de parámetros
     */
    renderSelectionInterface(container, dataMatrix, fileName, excelUrl) {

        //Marcar los checkboxes de grupo si todos sus hijos están marcados al inicio
        setTimeout(() => {
            document.querySelectorAll('.group-checkbox').forEach((groupCb, gIdx) => {
                const allChecked = Array.from(document.querySelectorAll(`.column-checkbox.group-${gIdx}`)).every(cb => cb.checked);
                groupCb.checked = allChecked;
            });
        }, 0);
        // --- NUEVA LÓGICA DE ARREGLOS ---
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

        // --- HTML ---
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

        // --- Renderizar checkboxes para RowsY (selección múltiple de filas) ---
        const rowsYContainer = document.getElementById('rowsYCheckboxes');
        // Checkbox seleccionar/deseleccionar todo
        rowsYContainer.innerHTML = `
            <div>
                <input type="checkbox" class="form-check-input" id="rowy-select-all" checked>
                <label class="form-check-label fw-bold" for="rowy-select-all">(Seleccionar/Deseleccionar todo)</label>
            </div>
        `;
        rowsYContainer.innerHTML += RowsY.map((row, idx) => `
            <div>
                <input type="checkbox" class="form-check-input rowy-checkbox" id="rowy-${idx}" value="${idx}" checked>
                <label class="form-check-label" for="rowy-${idx}">${row}</label>
            </div>
        `).join('');

        // --- Renderizar checkboxes jerárquicos para columnas (Eje Y) ---
        const groupedColumnCheckboxes = document.getElementById('groupedColumnCheckboxes');
        groupedColumnCheckboxes.innerHTML = GroupColsX.map((group, gIdx) => `
            <div class="mb-2 border rounded p-2">
                <div>
                    <input type="checkbox" class="form-check-input group-checkbox" id="group-${gIdx}">
                    <label class="form-check-label fw-bold" for="group-${gIdx}">${group.group}</label>
                </div>
                <div class="ms-3">
                    ${group.cols.map((col, cIdx) => {
                        // Si el grupo y la columna son iguales, solo muestra uno
                        const showLabel = (group.group === col) ? group.group : `${group.group} - ${col}`;
                        const fullLabel = (group.group === col) ? group.group : `${group.group} - ${col}`;
                        return `
                            <div>
                                <input type="checkbox" class="form-check-input column-checkbox group-${gIdx}" 
                                       id="col-${gIdx}-${cIdx}" 
                                       value="${col}" 
                                       data-group="${group.group}"
                                       data-full-label="${fullLabel}"
                                       checked>
                                <label class="form-check-label" for="col-${gIdx}-${cIdx}">${showLabel}</label>
                            </div>
                        `;
                    }).join('')}
                </div>
            </div>
        `).join('');

        // --- FUNCIÓN PARA ACTUALIZAR GRÁFICA EN TIEMPO REAL ---
        const updateChart = () => {
            // Obtener selecciones actuales
            const selectedRowIndices = Array.from(document.querySelectorAll('.rowy-checkbox:checked'))
                .map(cb => parseInt(cb.value, 10));
            
            // Obtener checkboxes de columnas seleccionadas
            const selectedColumnCheckboxes = Array.from(document.querySelectorAll('.column-checkbox:checked'));
            const selectedColNames = selectedColumnCheckboxes.map(cb => cb.value);
            const selectedColLabels = selectedColumnCheckboxes.map(cb => cb.getAttribute('data-full-label') || cb.value);

            if (selectedRowIndices.length === 0 || selectedColNames.length === 0) {
                // Limpiar gráfica si no hay selección
                const chartContainer = document.getElementById('chartContainer');
                if (this.chartInstance) {
                    this.chartInstance.destroy();
                    this.chartInstance = null;
                }
                chartContainer.innerHTML = '<div class="alert alert-info text-center"><i class="bi bi-info-circle me-1"></i>Selecciona filas y columnas para ver la gráfica</div>';
                return;
            }

            // Mapear nombres de columnas a índices (soportando nombres repetidos)
            let colIndices = [];
            if (tipoGrafica === "A") {
                // Buscar cada colName desde la última posición encontrada
                let lastPos = 0;
                selectedColNames.forEach((colName) => {
                    // Buscar desde lastPos en adelante
                    const headers = dataMatrix[1];
                    let found = -1;
                    for (let i = lastPos; i < headers.length; i++) {
                        if (headers[i] === colName) {
                            found = i;
                            lastPos = i + 1;
                            break;
                        }
                    }
                    if (found !== -1) {
                        colIndices.push(found);
                    }
                });
            } else {
                let lastPos = 0;
                selectedColNames.forEach((colName) => {
                    const headers = dataMatrix[0];
                    let found = -1;
                    for (let i = lastPos; i < headers.length; i++) {
                        if (headers[i] === colName) {
                            found = i;
                            lastPos = i + 1;
                            break;
                        }
                    }
                    if (found !== -1) {
                        colIndices.push(found);
                    }
                });
            }

            // Obtener tipo de gráfica seleccionado
            const chartTypeSelect = document.getElementById('chartType');
            const type = chartTypeSelect ? chartTypeSelect.value : 'bar';

            // Regenerar gráfica pasando también los labels descriptivos
            const chartContainer = document.getElementById('chartContainer');
            this.renderChartHierarchical(chartContainer, dataMatrix, selectedRowIndices, colIndices, type, tipoGrafica, selectedColLabels);
        };

        // --- Lógica de selección jerárquica de columnas ---
        groupedColumnCheckboxes.querySelectorAll('.group-checkbox').forEach((groupCb, gIdx) => {
            groupCb.addEventListener('change', function() {
                const checked = this.checked;
                groupedColumnCheckboxes.querySelectorAll(`.column-checkbox.group-${gIdx}`).forEach(cb => {
                    cb.checked = checked;
                });
                // Actualizar gráfica automáticamente
                updateChart();
            });
        });
        groupedColumnCheckboxes.querySelectorAll('.column-checkbox').forEach(cb => {
            cb.addEventListener('change', function() {
                GroupColsX.forEach((group, gIdx) => {
                    const allChecked = group.cols.every((col, cIdx) =>
                        groupedColumnCheckboxes.querySelector(`#col-${gIdx}-${cIdx}`).checked
                    );
                    groupedColumnCheckboxes.querySelector(`#group-${gIdx}`).checked = allChecked;
                });
                // Actualizar gráfica automáticamente
                updateChart();
            });
        });

        // --- Agregar listeners a checkboxes de filas (RowsY) ---
        // Listener para seleccionar/deseleccionar todo
        const selectAllRowsY = document.getElementById('rowy-select-all');
        selectAllRowsY.addEventListener('change', function() {
            const checked = this.checked;
            document.querySelectorAll('.rowy-checkbox').forEach(cb => {
                cb.checked = checked;
            });
            updateChart();
        });
        // Listener para cada checkbox individual
        document.querySelectorAll('.rowy-checkbox').forEach(cb => {
            cb.addEventListener('change', function() {
                // Si alguno se desmarca, desmarca el select-all
                if (!this.checked) {
                    selectAllRowsY.checked = false;
                } else {
                    // Si todos están marcados, marca el select-all
                    const allChecked = Array.from(document.querySelectorAll('.rowy-checkbox')).every(cb2 => cb2.checked);
                    selectAllRowsY.checked = allChecked;
                }
                updateChart();
            });
        });

        // --- Agregar listener al selector de tipo de gráfica ---
        const chartTypeSelect = document.getElementById('chartType');
        chartTypeSelect.addEventListener('change', updateChart);

        // --- Botón de renderización manual (opcional) ---
        const renderBtn = document.getElementById('renderChartBtn');
        renderBtn.addEventListener('click', updateChart);

        // --- CARGAR CHART.JS SI NO ESTÁ PRESENTE ---
        this.loadChartJS().then(() => {
            // Generar gráfica inicial automáticamente después de un pequeño delay
            setTimeout(updateChart, 200);
        }).catch(error => {
            console.error('Error cargando Chart.js:', error);
            const chartContainer = document.getElementById('chartContainer');
            chartContainer.innerHTML = '<div class="alert alert-danger">Error cargando Chart.js. <a href="https://cdn.jsdelivr.net/npm/chart.js" target="_blank">Cargar manualmente</a></div>';
        });
    }

    /**
     * Carga Chart.js dinámicamente si no está presente
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
        // Destruir gráfica anterior si existe
        if (this.chartInstance) {
            this.chartInstance.destroy();
            this.chartInstance = null;
        }

        // Validaciones
        if (!selectedRowIndices || selectedRowIndices.length === 0 || !yColIndices || yColIndices.length === 0) {
            container.innerHTML = '<div class="alert alert-warning text-center">No hay datos suficientes para mostrar la gráfica</div>';
            return;
        }

        // Etiquetas eje X: los valores seleccionados de RowsY
        let labels = [];
        if (tipoGrafica === "A") {
            labels = selectedRowIndices.map(idx => dataMatrix[idx + 2] ? dataMatrix[idx + 2][0] : '');
        } else {
            labels = selectedRowIndices.map(idx => dataMatrix[idx + 1] ? dataMatrix[idx + 1][0] : '');
        }

        // Datasets para cada columna seleccionada (Eje Y)
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

            // Para scatter, transformar a [{x, y}] (x=label, y=valor)
            if (type === 'scatter') {
                data = data.map((y, idx) => ({ x: labels[idx], y }));
            }

            // Para área, usar tipo 'line' y fill: true
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

        // Crear canvas
        const canvas = document.createElement('canvas');
        canvas.id = 'dynamicChart';
        canvas.height = 400;
        container.innerHTML = '';
        container.appendChild(canvas);

        const ctx = canvas.getContext('2d');

        // Ajustar tipo real de Chart.js
        let chartType = type;
        if (type === 'area') chartType = 'line';
        if (type === 'horizontalBar') chartType = 'bar'; // Chart.js v3+ usa bar con indexAxis

        const chartData = { labels, datasets };
        const options = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { 
                    display: true,
                    position: 'top'
                },
                tooltip: { 
                    enabled: true,
                    intersect: false,
                    mode: 'index'
                }
            },
            scales: (['bar','line','area','horizontalBar','scatter'].includes(type)) ? {
                x: type === 'horizontalBar' ? { beginAtZero: true, type: 'category' } : {},
                y: { beginAtZero: true }
            } : {}
        };
        // Para barra horizontal, cambiar orientación
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
            'rgba(255, 159, 64, 0.7)'
        ];
        return colors[index % colors.length];
    }
}

// Instancia global
window.GraficaModalEngine = new GraficaModalEngine();