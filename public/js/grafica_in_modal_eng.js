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

            // Determinar tipo de gráfica y extraer estructura
            const {
                CabeceraY,
                RowsY,
                GroupColsX,
                ColsX,
                isTipoA
            } = this.analyzeDataMatrix(dataMatrix);

            console.log('🔍 Análisis:');
            console.log('CabeceraY:', CabeceraY);
            console.log('RowsY:', RowsY);
            console.log('GroupColsX:', GroupColsX);
            console.log('ColsX:', ColsX);
            console.log('isTipoA:', isTipoA);

            // Mostrar interfaz de selección
            this.renderSelectionInterface(container, dataMatrix, CabeceraY, RowsY, GroupColsX, ColsX, isTipoA, fileName, excelUrl);

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
     * Analiza la matriz para determinar tipo y extraer estructura
     */
    analyzeDataMatrix(dataMatrix) {
        const isTipoA = dataMatrix[0][0] === '' || dataMatrix[0][0].trim() === '';
        const isTipoB = !isTipoA;

        // --- CabeceraY ---
        const CabeceraY = isTipoA ? dataMatrix[1][0] : dataMatrix[0][0];

        // --- RowsY ---
        const RowsY = isTipoA 
            ? dataMatrix.slice(2).map(row => row[0]) 
            : dataMatrix.slice(1).map(row => row[0]);

        // --- GroupColsX y ColsX ---
        const GroupColsX = {};
        const ColsX = [];

        if (isTipoA) {
            // Tipo A: Grupo de columnas anidadas
            let currentGroup = null;
            for (let i = 0; i < dataMatrix[0].length; i++) {
                const groupHeader = dataMatrix[0][i];
                if (groupHeader && groupHeader.trim() !== '') {
                    currentGroup = groupHeader;
                    GroupColsX[currentGroup] = [];
                }
                if (currentGroup) {
                    GroupColsX[currentGroup].push(i);
                }
            }

            // ColsX: nombres de columnas (fila 1, ignorando CabeceraY)
            ColsX.push(...dataMatrix[1].slice(1));
        } else {
            // Tipo B: Columnas simples
            ColsX.push(...dataMatrix[0].slice(1));
        }

        return {
            CabeceraY,
            RowsY,
            GroupColsX,
            ColsX,
            isTipoA
        };
    }

    /**
     * Renderiza la interfaz de selección de parámetros
     */
    renderSelectionInterface(container, dataMatrix, CabeceraY, RowsY, GroupColsX, ColsX, isTipoA, fileName, excelUrl) {
        const selectionHTML = `
            <div class="d-flex flex-column mb-3">
                <h6>Selecciona los datos para graficar:</h6>

                <!-- Eje X -->
                <div class="mb-3">
                    <label class="form-label">Seleccionar columna de eje X:</label>
                    <select id="xAxisSelect" class="form-select"></select>
                </div>

                <!-- Eje Y -->
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

        // --- Selección de eje X ---
        const xAxisSelect = document.getElementById('xAxisSelect');
        xAxisSelect.innerHTML = ColsX.map((col, idx) =>
            `<option value="${idx}">${col}</option>`
        ).join('');
        xAxisSelect.selectedIndex = 0;

        // --- Selección jerárquica de grupos y columnas (Eje Y) ---
        const groupedColumnCheckboxes = document.getElementById('groupedColumnCheckboxes');
        if (isTipoA) {
            // Tipo A: Grupos anidados
            groupedColumnCheckboxes.innerHTML = Object.entries(GroupColsX).map(([groupName, colIndices]) => `
                <div class="mb-2 border rounded p-2">
                    <div>
                        <input type="checkbox" class="form-check-input group-checkbox" id="group-${groupName}">
                        <label class="form-check-label fw-bold" for="group-${groupName}">${groupName}</label>
                    </div>
                    <div class="ms-3">
                        ${colIndices.map(idx => `
                            <div>
                                <input type="checkbox" class="form-check-input column-checkbox group-${groupName}" id="col-${idx}" value="${idx}">
                                <label class="form-check-label" for="col-${idx}">${ColsX[idx]}</label>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `).join('');
        } else {
            // Tipo B: Columnas simples
            groupedColumnCheckboxes.innerHTML = ColsX.map((col, idx) => `
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="col-${idx}" value="${idx}">
                    <label class="form-check-label" for="col-${idx}">${col}</label>
                </div>
            `).join('');
        }

        // --- Lógica de selección jerárquica ---
        if (isTipoA) {
            groupedColumnCheckboxes.querySelectorAll('.group-checkbox').forEach((groupCb, gIdx) => {
                groupCb.addEventListener('change', function() {
                    const checked = this.checked;
                    groupedColumnCheckboxes.querySelectorAll(`.column-checkbox.group-${this.id.split('-')[1]}`).forEach(cb => {
                        cb.checked = checked;
                    });
                });
            });

            groupedColumnCheckboxes.querySelectorAll('.column-checkbox').forEach(cb => {
                cb.addEventListener('change', function() {
                    const groupName = this.id.split('-')[1];
                    const allChecked = groupedColumnCheckboxes.querySelectorAll(`.column-checkbox.group-${groupName}`).every(cb => cb.checked);
                    groupedColumnCheckboxes.querySelector(`#group-${groupName}`).checked = allChecked;
                });
            });
        }

        // Seleccionar todo por defecto
        groupedColumnCheckboxes.querySelectorAll('.form-check-input').forEach(cb => cb.checked = true);

        // --- Botón de renderización ---
        const renderBtn = document.getElementById('renderChartBtn');
        const chartContainer = document.getElementById('chartContainer');
        const chartType = document.getElementById('chartType');

        renderBtn.addEventListener('click', () => {
            const xAxisIdx = parseInt(xAxisSelect.value, 10);

            // Obtener índices de columnas seleccionadas (Eje Y)
            const selectedColIndices = Array.from(groupedColumnCheckboxes.querySelectorAll('.column-checkbox:checked'))
                .map(cb => parseInt(cb.value, 10));

            if (selectedColIndices.length === 0) {
                alert('Por favor seleccione al menos una columna/grupo para el eje Y.');
                return;
            }

            this.renderChart(chartContainer, dataMatrix, xAxisIdx, selectedColIndices, chartType.value, isTipoA);
        });
    }

    /**
     * Genera la gráfica con Chart.js
     */
    renderChart(container, dataMatrix, xAxisIdx, yColIndices, type, isTipoA) {
        if (this.chartInstance) {
            this.chartInstance.destroy();
        }

        // Etiquetas eje X (filas de datos)
        const labels = isTipoA 
            ? dataMatrix.slice(2).map(row => row[0])
            : dataMatrix.slice(1).map(row => row[0]);

        // Datasets para cada columna seleccionada (Eje Y)
        const datasets = yColIndices.map((colIdx, i) => ({
            label: dataMatrix[isTipoA ? 1 : 0][colIdx],
            data: dataMatrix.slice(isTipoA ? 2 : 1).map(row => parseFloat(row[colIdx]) || 0),
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