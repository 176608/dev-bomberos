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

        // --- 1. Determinar HeaderEjeY ---
        const HeaderEjeY = firstColumn.find(val => val && val.trim().length > 0) || '';

        // --- 2. HeadersRowsY ---
        const HeadersRowsY = firstColumn.slice(1).filter(Boolean);

        // --- 3. GroupHeadersX ---
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
        const groups = {};
        const cols = headerRow.length;

        if (dataMatrix.length < 2) return { [headerRow[0]]: [] };

        const secondRow = dataMatrix[1];
        const hasSubheaders = secondRow.some(cell => cell && cell.trim().length > 0);

        if (!hasSubheaders) {
            return { [headerRow[0]]: [] };
        }

        // Encontrar encabezados principales
        const level1Headers = [];
        for (let i = 0; i < cols; i++) {
            const val = headerRow[i];
            if (val && !level1Headers.includes(val)) {
                level1Headers.push(val);
            }
        }

        // Crear grupos
        for (const group of level1Headers) {
            groups[group] = [];
        }

        // Asignar subencabezados
        for (let i = 0; i < cols; i++) {
            const mainHeader = headerRow[i];
            const subHeader = secondRow[i];

            if (mainHeader && groups[mainHeader]) {
                if (subHeader && subHeader.trim().length > 0) {
                    groups[mainHeader].push(subHeader);
                }
            }
        }

        // Retornar solo grupos con subencabezados
        const result = {};
        for (const [key, values] of Object.entries(groups)) {
            if (values.length > 0) {
                result[key] = values;
            } else {
                result[key] = [];
            }
        }

        return result;
    }

    /**
     * Renderiza la interfaz de selección de parámetros
     */
    renderSelectionInterface(container, HeaderEjeY, HeadersRowsY, GroupHeadersX, dataMatrix, fileName, excelUrl) {
        const selectionHTML = `
            <div class="d-flex flex-column mb-3">
                <h6>Selecciona los datos para graficar:</h6>

                <!-- Grupo -->
                <div class="mb-3">
                    <label class="form-label">Grupo de columnas:</label>
                    <select id="groupSelect" class="form-select">
                        <option value="">-- Seleccione un grupo --</option>
                        ${Object.keys(GroupHeadersX).map(key => 
                            `<option value="${key}">${key}</option>`
                        ).join('')}
                    </select>
                </div>

                <!-- Columnas del grupo -->
                <div class="mb-3" id="columnsContainer">
                    <label class="form-label">Seleccionar columnas:</label>
                    <div id="columnCheckboxes"></div>
                </div>

                <!-- Filas Y -->
                <div class="mb-3">
                    <label class="form-label">Seleccionar filas:</label>
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

        // Manejar eventos
        const groupSelect = document.getElementById('groupSelect');
        const chartType = document.getElementById('chartType');
        const renderBtn = document.getElementById('renderChartBtn');
        const chartContainer = document.getElementById('chartContainer');
        const columnsContainer = document.getElementById('columnCheckboxes');
        const rowCheckboxes = document.getElementById('rowCheckboxes');

        // Actualizar columnas cuando cambia el grupo
        groupSelect.addEventListener('change', () => {
            const selectedGroup = groupSelect.value;
            if (!selectedGroup) {
                columnsContainer.innerHTML = '';
                return;
            }

            const subHeaders = GroupHeadersX[selectedGroup];
            columnsContainer.innerHTML = subHeaders.map(header => `
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="col-${header}" value="${header}">
                    <label class="form-check-label" for="col-${header}">${header}</label>
                </div>
            `).join('');
        });

        // Generar checkboxes para filas
        rowCheckboxes.innerHTML = HeadersRowsY.map(label => `
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="row-${label}" value="${label}">
                <label class="form-check-label" for="row-${label}">${label}</label>
            </div>
        `).join('');

        // Habilitar todos los checkboxes por defecto
        document.querySelectorAll('.form-check-input').forEach(cb => cb.checked = true);

        // Botón de renderización
        renderBtn.addEventListener('click', () => {
            const selectedGroup = groupSelect.value;
            if (!selectedGroup) {
                alert('Por favor seleccione un grupo.');
                return;
            }

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

            this.renderChart(chartContainer, dataMatrix, selectedRows, selectedGroup, selectedColumns, type);
        });
    }

    /**
     * Genera la gráfica con Chart.js
     */
    renderChart(container, dataMatrix, selectedRows, selectedGroup, selectedColumns, type) {
        // Limpiar contenedores anteriores
        if (this.chartInstance) {
            this.chartInstance.destroy();
        }

        // Extraer datos para cada columna seleccionada
        const datasets = [];
        const labels = [];

        // Buscar índices de las columnas seleccionadas
        const headers = dataMatrix[0];
        const colIndices = selectedColumns.map(col => {
            const idx = headers.findIndex(h => h === col);
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

        // Construir etiquetas
        labels.push(...selectedRows);

        // Construir datasets
        colIndices.forEach((colIndex, i) => {
            const data = [];
            rowIndices.forEach(rowIndex => {
                const value = parseFloat(dataMatrix[rowIndex][colIndex]) || 0;
                data.push(value);
            });

            datasets.push({
                label: selectedColumns[i],
                data: data,
                backgroundColor: this.getColor(i),
                borderColor: this.getColor(i),
                borderWidth: 1
            });
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