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

        // --- Cargar Choices.js y CSS si no está presente ---
        function loadChoicesAssets() {
            return new Promise((resolve) => {
                if (window.Choices) return resolve();
                // CSS
                if (!document.getElementById('choicesjs-css')) {
                    const link = document.createElement('link');
                    link.rel = 'stylesheet';
                    link.id = 'choicesjs-css';
                    link.href = 'https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css';
                    document.head.appendChild(link);
                }
                // JS
                const script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js';
                script.onload = resolve;
                document.head.appendChild(script);
            });
        }

        // --- HTML: select múltiple para ambos ejes ---
        const selectionHTML = `
            <div class="row g-2 align-items-start mb-2">
                <div class="col-12 col-md-6">
                    <label class="form-label mb-1"><small><b>${CabeceraY}:</b></small></label>
                    <select id="rowsYSelect" multiple></select>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label mb-1"><small><b>Columnas/grupos:</b></small></label>
                    <select id="colsXSelect" multiple></select>
                </div>
            </div>
            <div class="row g-2 mt-2 mb-2">
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

        // --- Inicializar Choices.js después de cargar assets ---
        loadChoicesAssets().then(() => {
            // Eje Y (Rows)
            const rowsYSelect = document.getElementById('rowsYSelect');
            rowsYSelect.innerHTML = RowsY.map((row, idx) => `<option value="${idx}" selected>${row}</option>`).join('');
            const rowsYChoices = new Choices(rowsYSelect, {
                removeItemButton: true,
                shouldSort: false,
                placeholder: true,
                placeholderValue: 'Seleccione...',
                searchPlaceholderValue: 'Buscar...',
                itemSelectText: '',
                maxItemCount: 100,
                classNames: { containerOuter: 'choices choices-rowsy' }
            });

            // Eje X (Cols) con optgroups
            const colsXSelect = document.getElementById('colsXSelect');
            let colIdx = 0;
            colsXSelect.innerHTML = GroupColsX.map(group => {
                return `<optgroup label="${group.group}">` +
                    group.cols.map(col => `<option value="${colIdx++}" selected>${col}</option>`).join('') +
                `</optgroup>`;
            }).join('');
            const colsXChoices = new Choices(colsXSelect, {
                removeItemButton: true,
                shouldSort: false,
                placeholder: true,
                placeholderValue: 'Seleccione...',
                searchPlaceholderValue: 'Buscar...',
                itemSelectText: '',
                maxItemCount: 100,
                classNames: { containerOuter: 'choices choices-colsx' }
            });

            // --- Botones Marcar todas / Desmarcar todas ---
            function addSelectAllBtns(choicesInstance, selectEl) {
                const container = selectEl.closest('.col-12');
                if (!container) return;
                let btns = document.createElement('div');
                btns.className = 'mb-1';
                btns.innerHTML = `
                    <button type="button" class="btn btn-link btn-sm p-0 me-2" style="color:#21807a;font-weight:bold;" tabindex="-1">✔ Marca todas</button>
                    <button type="button" class="btn btn-link btn-sm p-0" style="color:#21807a;font-weight:bold;" tabindex="-1">✖ Desmarque todas</button>
                `;
                const [btnAll, btnNone] = btns.querySelectorAll('button');
                btnAll.onclick = () => {
                    Array.from(selectEl.options).forEach(opt => { opt.selected = true; });
                    choicesInstance.setChoiceByValue(Array.from(selectEl.options).map(opt => opt.value));
                };
                btnNone.onclick = () => {
                    Array.from(selectEl.options).forEach(opt => { opt.selected = false; });
                    choicesInstance.removeActiveItems();
                };
                container.insertBefore(btns, selectEl);
            }
            addSelectAllBtns(rowsYChoices, rowsYSelect);
            addSelectAllBtns(colsXChoices, colsXSelect);

            // --- Actualizar gráfica al cambiar selección ---
            rowsYSelect.addEventListener('change', updateChart);
            colsXSelect.addEventListener('change', updateChart);

            // --- Actualizar gráfica manual ---
            document.getElementById('renderChartBtn').addEventListener('click', updateChart);

            // --- Actualizar gráfica automáticamente al cargar ---
            setTimeout(updateChart, 200);
        });

        // --- UX: Colapsar/expandir Eje Y y Eje X, guardar estado en localStorage ---
        function getCollapseKey(which) {
            return 'grafica_colapso_' + which;
        }
        function setCollapsed(which, collapsed) {
            localStorage.setItem(getCollapseKey(which), collapsed ? '1' : '0');
        }
        function isCollapsed(which) {
            return localStorage.getItem(getCollapseKey(which)) === '1';
        }
        // Eje Y
        const rowsYDiv = document.getElementById('rowsYCheckboxes');
        const btnRowsY = document.getElementById('toggleRowsY');
        function updateRowsYCollapse() {
            if (isCollapsed('rowsY')) {
                rowsYDiv.style.display = 'none';
                btnRowsY.innerHTML = '►';
            } else {
                rowsYDiv.style.display = '';
                btnRowsY.innerHTML = '▼';
            }
        }
        btnRowsY.addEventListener('click', function() {
            setCollapsed('rowsY', !isCollapsed('rowsY'));
            updateRowsYCollapse();
        });
        updateRowsYCollapse();
        // Eje X
        const colsXDiv = document.getElementById('groupedColumnCheckboxes');
        const btnColsX = document.getElementById('toggleColsX');
        function updateColsXCollapse() {
            if (isCollapsed('colsX')) {
                colsXDiv.style.display = 'none';
                btnColsX.innerHTML = '►';
            } else {
                colsXDiv.style.display = '';
                btnColsX.innerHTML = '▼';
            }
        }
        btnColsX.addEventListener('click', function() {
            setCollapsed('colsX', !isCollapsed('colsX'));
            updateColsXCollapse();
        });
        updateColsXCollapse();




        // --- FUNCIÓN PARA ACTUALIZAR GRÁFICA EN TIEMPO REAL (Choices.js) ---
        const updateChart = () => {
            // Obtener selecciones actuales
            const rowsYSelect = document.getElementById('rowsYSelect');
            const colsXSelect = document.getElementById('colsXSelect');
            const selectedRowIndices = Array.from(rowsYSelect.selectedOptions).map(opt => parseInt(opt.value, 10));
            const selectedColIndices = Array.from(colsXSelect.selectedOptions).map(opt => parseInt(opt.value, 10));
            const selectedColLabels = Array.from(colsXSelect.selectedOptions).map(opt => opt.textContent);

            if (selectedRowIndices.length === 0 || selectedColIndices.length === 0) {
                // Limpiar gráfica si no hay selección
                const chartContainer = document.getElementById('chartContainer');
                if (this.chartInstance) {
                    this.chartInstance.destroy();
                    this.chartInstance = null;
                }
                chartContainer.innerHTML = '<div class="alert alert-info text-center"><i class="bi bi-info-circle me-1"></i>Selecciona filas y columnas para ver la gráfica</div>';
                return;
            }

            // Obtener tipo de gráfica seleccionado
            const chartTypeSelect = document.getElementById('chartType');
            const type = chartTypeSelect ? chartTypeSelect.value : 'bar';

            // Regenerar gráfica pasando también los labels descriptivos
            const chartContainer = document.getElementById('chartContainer');
            this.renderChartHierarchical(chartContainer, dataMatrix, selectedRowIndices, selectedColIndices, type, tipoGrafica, selectedColLabels);
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