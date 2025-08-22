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
            <div class="d-flex flex-column mb-3">
                <h6>Selecciona los datos para graficar:</h6>

                <!-- CabeceraY -->
                <div class="mb-3">
                    <label class="form-label">Eje X (${CabeceraY}):</label>
                    <div id="rowsYCheckboxes"></div>
                </div>

                <!-- Columnas/Grupos (Eje Y) -->
                <div class="mb-3">
                    <label class="form-label">Seleccionar columnas/grupos (Eje Y):</label>
                    <div id="groupedColumnCheckboxes"></div>
                </div>

                <!-- Tipo de gráfica -->
                <div class="mb-3">
                    <label class="form-label">Tipo de gráfica:</label>
                    <select id="chartType" class="form-select">
                        <option value="bar">Barra</option>
                        <option value="line">Línea</option>
                        <option value="pie">Pastel</option>
                    </select>
                </div>

                <button id="renderChartBtn" class="btn btn-primary">Generar Gráfica</button>
            </div>
            <div id="chartContainer" class="mb-3"></div>
        `;
        container.innerHTML = selectionHTML;

        // --- Renderizar checkboxes para RowsY (selección múltiple de filas) ---
        const rowsYContainer = document.getElementById('rowsYCheckboxes');
        rowsYContainer.innerHTML = RowsY.map((row, idx) => `
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
                    ${group.cols.map((col, cIdx) => `
                        <div>
                            <input type="checkbox" class="form-check-input column-checkbox group-${gIdx}" id="col-${gIdx}-${cIdx}" value="${col}" checked>
                            <label class="form-check-label" for="col-${gIdx}-${cIdx}">${col}</label>
                        </div>
                    `).join('')}
                </div>
            </div>
        `).join('');

        // --- Lógica de selección jerárquica de columnas ---
        groupedColumnCheckboxes.querySelectorAll('.group-checkbox').forEach((groupCb, gIdx) => {
            groupCb.addEventListener('change', function() {
                const checked = this.checked;
                groupedColumnCheckboxes.querySelectorAll(`.column-checkbox.group-${gIdx}`).forEach(cb => {
                    cb.checked = checked;
                });
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
            });
        });

        // --- Botón de renderización ---
        const renderBtn = document.getElementById('renderChartBtn');
        const chartContainer = document.getElementById('chartContainer');
        const chartType = document.getElementById('chartType');

        renderBtn.addEventListener('click', () => {
            // Obtener índices de filas seleccionadas (RowsY)
            const selectedRowIndices = Array.from(document.querySelectorAll('.rowy-checkbox:checked'))
                .map(cb => parseInt(cb.value, 10));

            if (selectedRowIndices.length === 0) {
                alert('Por favor selecciona al menos una fila para el eje X.');
                return;
            }

            // Obtener nombres de columnas seleccionadas (Eje Y)
            const selectedColNames = Array.from(groupedColumnCheckboxes.querySelectorAll('.column-checkbox:checked'))
                .map(cb => cb.value);

            if (selectedColNames.length === 0) {
                alert('Por favor selecciona al menos una columna/grupo para el eje Y.');
                return;
            }

            // Mapear nombres de columnas a índices reales en dataMatrix
            let colIndices = [];
            if (tipoGrafica === "A") {
                // Buscar en dataMatrix[1]
                colIndices = selectedColNames.map(colName => dataMatrix[1].indexOf(colName));
            } else {
                // Buscar en dataMatrix[0]
                colIndices = selectedColNames.map(colName => dataMatrix[0].indexOf(colName));
            }

            // Llamar a la función de graficar con los parámetros seleccionados
            this.renderChartHierarchical(chartContainer, dataMatrix, selectedRowIndices, colIndices, chartType.value, tipoGrafica);
        });
    }

    /**
     * Genera la gráfica con Chart.js usando selección múltiple de filas y columnas
     */
    renderChartHierarchical(container, dataMatrix, selectedRowIndices, yColIndices, type, tipoGrafica) {
        if (this.chartInstance) {
            this.chartInstance.destroy();
        }

        // Etiquetas eje X: los valores seleccionados de RowsY
        let labels = [];
        if (tipoGrafica === "A") {
            labels = selectedRowIndices.map(idx => dataMatrix[idx + 2][0]);
        } else {
            labels = selectedRowIndices.map(idx => dataMatrix[idx + 1][0]);
        }

        // Datasets para cada columna seleccionada (Eje Y)
        const datasets = yColIndices.map((colIdx, i) => ({
            label: (tipoGrafica === "A" ? dataMatrix[1][colIdx] : dataMatrix[0][colIdx]),
            data: selectedRowIndices.map(idx => {
                if (tipoGrafica === "A") {
                    return parseFloat(dataMatrix[idx + 2][colIdx]) || 0;
                } else {
                    return parseFloat(dataMatrix[idx + 1][colIdx]) || 0;
                }
            }),
            backgroundColor: this.getColor(i),
            borderColor: this.getColor(i),
            borderWidth: 1
        }));

        // Crear canvas
        const canvas = document.createElement('canvas');
        canvas.id = 'dynamicChart';
        canvas.height = 320;
        container.innerHTML = '';
        container.appendChild(canvas);

        const ctx = canvas.getContext('2d');

        const chartData = { labels, datasets };
        const options = {
            responsive: true,
            plugins: {
                legend: { display: true },
                tooltip: { enabled: true }
            }
        };

        this.chartInstance = new Chart(ctx, {
            type: type,
            data: chartData,
            options: options
        });
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