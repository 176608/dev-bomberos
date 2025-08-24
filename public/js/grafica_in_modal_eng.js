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
 * Renderiza la interfaz de selección con dropdowns flotantes
 */
renderSelectionInterface(container, dataMatrix, fileName, excelUrl) {
    // --- LÓGICA DE DATOS ---
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

    // --- HTML con dropdowns ---
    const selectionHTML = `
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="dropdown" id="rowsYDropdown">
                <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    ${CabeceraY}
                </button>
                <div class="dropdown-menu p-0" style="max-height: 300px; overflow-y: auto;">
                    <div class="p-2 small">
                        <input type="checkbox" class="form-check-input" id="rowy-select-all" checked>
                        <label class="form-check-label fw-bold" for="rowy-select-all">(Seleccionar/Deseleccionar todo)</label>
                    </div>
                    <div id="rowsYCheckboxes" class="small"></div>
                </div>
            </div>
            <div class="dropdown" id="colsXDropdown">
                <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Columnas/grupos
                </button>
                <div class="dropdown-menu p-0" style="max-height: 300px; overflow-y: auto;">
                    <div id="groupedColumnCheckboxes" class="small"></div>
                </div>
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
        <div id="chartContainer" class="mb-3"></div>
    `;
    container.innerHTML = selectionHTML;

    // --- Renderizar checkboxes para RowsY ---
    const rowsYContainer = document.getElementById('rowsYCheckboxes');
    rowsYContainer.innerHTML = RowsY.map((row, idx) => `
        <div>
            <input type="checkbox" class="form-check-input rowy-checkbox" id="rowy-${idx}" value="${idx}" checked>
            <label class="form-check-label" for="rowy-${idx}">${row}</label>
        </div>
    `).join('');

    // --- Renderizar checkboxes jerárquicos para columnas ---
    const groupedColumnCheckboxes = document.getElementById('groupedColumnCheckboxes');
    groupedColumnCheckboxes.innerHTML = GroupColsX.map((group, gIdx) => `
        <div class="mb-2 border rounded p-2">
            <div>
                <input type="checkbox" class="form-check-input group-checkbox" id="group-${gIdx}">
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

    // --- Sincronizar estados iniciales ---
    this.syncGroupCheckboxes();

    // --- Eventos ---
    this.setupEventListeners(dataMatrix, tipoGrafica, container);

    // --- Cargar Chart.js ---
    this.loadChartJS().then(() => {
        setTimeout(() => this.updateChart(), 200);
    }).catch(error => {
        console.error('Error cargando Chart.js:', error);
        const chartContainer = document.getElementById('chartContainer');
        chartContainer.innerHTML = '<div class="alert alert-danger">Error cargando Chart.js.</div>';
    });

    // --- Configurar dropdowns ---
    this.setupDropdowns();
}

/**
 * Configura los dropdowns para que se cierren al hacer clic fuera
 */
setupDropdowns() {
    // Cerrar dropdowns al hacer clic fuera
    document.addEventListener('click', (e) => {
        const dropdowns = document.querySelectorAll('.dropdown-menu');
        dropdowns.forEach(menu => {
            if (!menu.contains(e.target) && !e.target.closest('.dropdown-toggle')) {
                menu.classList.remove('show');
            }
        });
    });

    // Asegurar que los dropdowns se cierren si se hace clic en el botón
    document.querySelectorAll('.dropdown-toggle').forEach(btn => {
        btn.addEventListener('click', function() {
            const menu = this.nextElementSibling;
            menu.classList.toggle('show');
        });
    });
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