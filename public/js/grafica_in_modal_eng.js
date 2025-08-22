/**
 * GraficaModalEngine - Motor para visualizar gráficas en modales desde Excel
 * @class
 */
class GraficaModalEngine {
    constructor() {
        this.chart = null;
        this.data = null;
        this.selectedGroup = null;
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

            // Extraer datos estructurados
            const {
                HeaderEjeY,
                HeadersRowsY,
                GroupHeadersX,
                dataMatrix
            } = this.extractDataFromWorksheet(worksheet, range);

            console.log('📊 Datos extraídos:');
            console.log('HeaderEjeY:', HeaderEjeY);
            console.log('HeadersRowsY:', HeadersRowsY);
            console.log('GroupHeadersX:', GroupHeadersX);
            console.log('dataMatrix:', dataMatrix);

            // Mostrar interfaz de selección
            this.renderSelectionInterface(container, HeaderEjeY, HeadersRowsY, GroupHeadersX, dataMatrix, fileName, excelUrl);

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
     * Extrae datos estructurados del worksheet
     */
    extractDataFromWorksheet(worksheet, range) {
        const dataMatrix = [];
        const headerRow = [];
        const firstColumn = [];

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
                if (c === range.s.c) {
                    firstColumn.push(value);
                }
                row.push(value);
            }
            dataMatrix.push(row);
        }

        // Determinar HeaderEjeY (primera columna)
        const HeaderEjeY = firstColumn[0] || '';

        // HeadersRowsY (todo lo que sigue bajo el HeaderEjeY)
        const HeadersRowsY = firstColumn.slice(1).filter(Boolean);

        // GroupHeadersX (estructura de encabezados horizontales)
        const GroupHeadersX = this.parseGroupHeadersX(headerRow, dataMatrix);

        return {
            HeaderEjeY,
            HeadersRowsY,
            GroupHeadersX,
            dataMatrix
        };
    }

    /**
     * Analiza los encabezados de columnas para detectar grupos
     */
    parseGroupHeadersX(headerRow, dataMatrix) {
        const groupHeaders = {};
        const cols = headerRow.length;

        // Detectar niveles de encabezado
        const level1Headers = [];
        const level2Headers = [];

        for (let i = 0; i < cols; i++) {
            const val = headerRow[i];
            if (val && !level1Headers.includes(val)) {
                level1Headers.push(val);
            }
        }

        // Si hay más de una fila de datos, analizar si hay subencabezados
        if (dataMatrix.length > 1) {
            const secondRow = dataMatrix[1];
            const thirdRow = dataMatrix[2];

            // Si hay datos en la segunda fila, es posible que sean subencabezados
            const hasSubheaders = secondRow.some(cell => cell && cell.trim().length > 0);

            if (hasSubheaders) {
                // Crear grupos por nivel 1
                let currentGroup = '';
                const groups = {};

                for (let i = 0; i < cols; i++) {
                    const val = headerRow[i];
                    const subVal = secondRow[i];

                    if (val && !groups[val]) {
                        groups[val] = [];
                    }

                    if (subVal && groups[val]) {
                        groups[val].push(subVal);
                    }
                }

                return groups;
            }
        }

        // Caso simple: solo uno nivel
        return { [headerRow[0]]: [] };
    }

    /**
     * Renderiza la interfaz de selección de parámetros
     */
    renderSelectionInterface(container, HeaderEjeY, HeadersRowsY, GroupHeadersX, dataMatrix, fileName, excelUrl) {
        const selectionHTML = `
            <div class="d-flex flex-column mb-3">
                <h6>Selecciona el grupo de datos para graficar:</h6>
                <div class="mb-2">
                    <label class="form-label">Grupo:</label>
                    <select id="groupSelect" class="form-select">
                        <option value="">-- Seleccione un grupo --</option>
                        ${Object.keys(GroupHeadersX).map(key => 
                            `<option value="${key}">${key}</option>`
                        ).join('')}
                    </select>
                </div>
                <div class="mb-2">
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

        // Manejar eventos
        const groupSelect = document.getElementById('groupSelect');
        const chartType = document.getElementById('chartType');
        const renderBtn = document.getElementById('renderChartBtn');
        const chartContainer = document.getElementById('chartContainer');

        renderBtn.addEventListener('click', () => {
            const selectedGroup = groupSelect.value;
            const type = chartType.value;

            if (!selectedGroup) {
                alert('Por favor seleccione un grupo de datos.');
                return;
            }

            this.renderChart(chartContainer, dataMatrix, HeadersRowsY, selectedGroup, type);
        });
    }

    /**
     * Genera la gráfica con Chart.js
     */
    renderChart(container, dataMatrix, labelsY, selectedGroup, type) {
        // Limpiar contenedores anteriores
        if (this.chartInstance) {
            this.chartInstance.destroy();
        }

        // Extraer datos para el grupo seleccionado
        const dataForGroup = this.getDataForGroup(dataMatrix, labelsY, selectedGroup);

        // Crear canvas
        const canvas = document.createElement('canvas');
        canvas.id = 'dynamicChart';
        canvas.height = 320;
        container.innerHTML = '';
        container.appendChild(canvas);

        const ctx = canvas.getContext('2d');

        // Configurar datos para Chart.js
        const chartData = {
            labels: labelsY,
            datasets: [{
                label: selectedGroup,
                data: dataForGroup,
                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
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
     * Obtiene datos para un grupo específico
     */
    getDataForGroup(dataMatrix, labelsY, groupName) {
        const result = [];
        const headers = dataMatrix[0];
        const rowIndex = headers.findIndex(h => h === groupName);

        if (rowIndex === -1) {
            // Buscar en subencabezados
            for (let i = 0; i < headers.length; i++) {
                if (headers[i] === groupName) {
                    // Aquí puedes hacer lógica adicional si hay múltiples columnas
                    break;
                }
            }
        }

        // Simulación: tomar primer valor de cada fila
        for (let i = 1; i < dataMatrix.length; i++) {
            const row = dataMatrix[i];
            const value = parseFloat(row[rowIndex]) || 0;
            result.push(value);
        }

        return result;
    }
}

// Instancia global
window.GraficaModalEngine = new GraficaModalEngine();