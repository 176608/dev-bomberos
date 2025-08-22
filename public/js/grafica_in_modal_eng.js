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
        const selectionHTML = `
            <div class="d-flex flex-column mb-3">
                <h6>Selecciona los datos para graficar:</h6>

                <!-- Columnas X -->
                <div class="mb-3">
                    <label class="form-label">Seleccionar columnas (Eje X):</label>
                    <div id="columnCheckboxes"></div>
                </div>

                <!-- Filas Y -->
                <div class="mb-3">
                    <label class="form-label">Seleccionar filas (Eje Y):</label>
                    <div id="rowCheckboxes"></div>
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

        // Generar checkboxes para columnas (Eje X)
        const headers = dataMatrix[0];
        const columnsContainer = document.getElementById('columnCheckboxes');
        columnsContainer.innerHTML = headers.map(header => `
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="col-${header}" value="${header}">
                <label class="form-check-label" for="col-${header}">${header}</label>
            </div>
        `).join('');

        // Generar checkboxes para filas (Eje Y)
        const rowCheckboxes = document.getElementById('rowCheckboxes');
        rowCheckboxes.innerHTML = dataMatrix.slice(1).map((row, i) => {
            const label = row[0] || `Fila ${i + 1}`;
            return `
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="row-${i}" value="${label}">
                    <label class="form-check-label" for="row-${i}">${label}</label>
                </div>
            `;
        }).join('');

        // Habilitar todos los checkboxes por defecto
        document.querySelectorAll('.form-check-input').forEach(cb => cb.checked = true);

        // Botón de renderización
        const renderBtn = document.getElementById('renderChartBtn');
        const chartContainer = document.getElementById('chartContainer');
        const chartType = document.getElementById('chartType');

        renderBtn.addEventListener('click', () => {
            const selectedColumns = Array.from(document.querySelectorAll('#columnCheckboxes .form-check-input:checked'))
                .map(cb => cb.value);

            const selectedRows = Array.from(document.querySelectorAll('#rowCheckboxes .form-check-input:checked'))
                .map(cb => cb.value);

            const type = chartType.value;

            if (selectedColumns.length === 0) {
                alert('Por favor seleccione al menos una columna.');
                return;
            }

            if (selectedRows.length === 0) {
                alert('Por favor seleccione al menos una fila.');
                return;
            }

            this.renderChart(chartContainer, dataMatrix, selectedRows, selectedColumns, type);
        });
    }

    /**
     * Genera la gráfica con Chart.js
     */
    renderChart(container, dataMatrix, selectedRows, selectedColumns, type) {
        // Limpiar contenedores anteriores
        if (this.chartInstance) {
            this.chartInstance.destroy();
        }

        // Buscar índices de las columnas seleccionadas
        const colIndices = selectedColumns.map(col => {
            const idx = dataMatrix[0].findIndex(h => h === col);
            return idx >= 0 ? idx : -1;
        }).filter(idx => idx !== -1);

        // Construir etiquetas (filas seleccionadas)
        const rowIndices = selectedRows.map(row => {
            const idx = dataMatrix.findIndex(r => r[0] === row);
            return idx >= 0 ? idx : -1;
        }).filter(idx => idx !== -1);

        // Si no hay filas válidas, mostrar error
        if (rowIndices.length === 0) {
            container.innerHTML = '<div class="alert alert-warning">No se encontraron datos para las filas seleccionadas.</div>';
            return;
        }

        // Construir etiquetas para el eje Y
        const labels = rowIndices.map(i => dataMatrix[i][0]);

        // Construir datasets para cada columna seleccionada
        const datasets = colIndices.map((colIndex, i) => {
            const data = rowIndices.map(rowIndex => {
                const value = parseFloat(dataMatrix[rowIndex][colIndex]) || 0;
                return value;
            });

            return {
                label: selectedColumns[i],
                data: data,
                backgroundColor: this.getColor(i),
                borderColor: this.getColor(i),
                borderWidth: 1
            };
        });

        // Crear canvas
        const canvas = document.createElement('canvas');
        canvas.id = 'dynamicChart';
        canvas.height = 320;
        container.innerHTML = '';
        container.appendChild(canvas);

        const ctx = canvas.getContext('2d');

        // Configurar datos para Chart.js
        const chartData = {
            labels: labels,
            datasets: datasets
        };

        // Opciones
        const options = {
            responsive: true,
            plugins: {
                legend: { display: true },
                tooltip: { enabled: true }
            }
        };

        // Crear gráfica
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