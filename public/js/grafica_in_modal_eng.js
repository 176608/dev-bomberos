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

        if (!document.getElementById('grafica-modal-zindex-style')) {
            const style = document.createElement('style');
            style.id = 'grafica-modal-zindex-style';
            style.innerHTML = `
                .only-child-duplicate {
                    display: none !important;
                }

                .grafica-modal-dropdown, .grafica-modal-select, .grafica-modal-checkbox-list, .grafica-modal-dropdown-menu {
                    position: absolute !important;
                    z-index: 3000 !important;
                    background: white;
                    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
                }

                .grafica-modal-checkbox-list label:hover,
                .grafica-modal-checkbox-list input[type="checkbox"]:focus + label {
                color: #016cd6ff;
                background: #90c9f7ad;
                }
                .grafica-modal-checkbox-list input[type="checkbox"]:focus {
                outline: 2px solid #1976d2;
                }
                .grafica-modal-checkbox-list .sticky-header {
                position: sticky;
                top: 0;
                background: #c9f3f1ff;
                z-index: 2;
                padding-bottom: 0.5rem;
                border-bottom: 1px solid #e0e0e0;
                }
                .grafica-modal-checkbox-list .sticky-header label {
                font-weight: bold;
                }
                .grafica-modal-checkbox-list .only-child-duplicate {
                display: none !important;
                }
                .grafica-modal-checkbox-list .group-checkbox {
                margin-right: 4px;
                }
                .grafica-modal-checkbox-list .column-checkbox {
                margin-right: 4px;
                }
                .grafica-modal-checkbox-list .form-check-label {
                font-size: 1rem;
                }
                .grafica-modal-checkbox-list .form-check-input {
                accent-color: #1976d2;
                }
                .grafica-modal-checkbox-list .checkbox-group-title {
                    font-weight: bold;
                    font-size: 1.08rem;
                    margin-bottom: 0.2rem;
                    display: flex;
                    align-items: center;
                }
                .grafica-modal-checkbox-list .checkbox-group {
                margin-bottom: 0.7rem;
                }
                .grafica-modal-checkbox-list .checkbox-group:last-child {
                margin-bottom: 0;
                }

                .grafica-toggle-btn {
                    background: rgba(151, 240, 210, 1)!important;
                    border-left: 4px solid #535353ff !important;
                    padding: 0.25rem 0.5rem;
                    width: 100%;
                    transition: background-color 0.18s ease, box-shadow 0.12s ease, transform 0.06s ease;
                    border-radius: 6px;
                    display: flex;
                    gap: 0.5rem;
                }
                .grafica-toggle-btn .form-label { flex: 1; }
                .grafica-toggle-btn:hover {
                    background: rgba(135, 123, 199, 1)!important;
                    cursor: pointer;
                    box-shadow: 0 1px 6px rgba(0,0,0,0.06);
                    border-left: 4px solid #0a0909ff !important;
                    transform: translateY(-2px);
                }
                .grafica-toggle-btn.saved {
                    background: #abe4bcff !important;
                    color: #155724 !important;
                    border-left: 4px solid #28a745 !important;
                }
                .grafica-toggle-btn.pressed {
                    background: #53c074ff !important;
                    color: #02290cff !important;
                    border-left: 4px solid #00360dff !important;
                    transform: translateY(2px);
                }
                /* Close button for panels */
                .grafica-close-btn {
                    background: transparent;
                    border: 0;
                    color: #666;
                    font-size: 1rem;
                    line-height: 1;
                    padding: 0.125rem 0.4rem;
                    border-radius: 4px;
                    cursor: pointer;
                }
                .grafica-close-btn:hover { background: rgba(0,0,0,0.04); color: #000; }
                /* Draggable content hint */
                .draggable-content { cursor: grab; }
                .draggable-content.dragging { opacity: 0.6; }
            `;
            document.head.appendChild(style);
        }

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

        const selectionHTML = `
            <div class="row g-2 align-items-start mb-2">
                <div class="col-12 col-md-6 draggable-panel" data-panel="rowsY" draggable="true">
                    <!-- Botón ahora envuelve la etiqueta para ocupar todo el espacio -->
                    <button id="toggleRowsY" type="button" class="grafica-toggle-btn btn p-0 d-flex justify-content-between align-items-center mb-1 w-100 text-start" style="background:none;border:0;" aria-expanded="true">
                        <div class="form-label mb-0 text-center"><small><b>${CabeceraY}:</b></small></div>
                        <div id="toggleRowsYIcon" style="font-size:1.5em;"></div>
                    </button>
                    <div id="rowsYCheckboxes" class="grafica-modal-checkbox-list draggable-content" draggable="true">
                        <div style="position:absolute;right:8px;top:6px;z-index:4;">
                            <button class="grafica-close-btn" data-target="rowsY" title="Cerrar sección">✕</button>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 draggable-panel" data-panel="colsX" draggable="true">
                    <!-- Igual para columnas -->
                    <button id="toggleColsX" type="button" class="grafica-toggle-btn btn p-0 d-flex justify-content-between align-items-center mb-1 w-100 text-start" style="background:none;border:0;" aria-expanded="true">
                        <div class="form-label mb-0 text-center"><small><b>Columnas/grupos:</b></small></div>
                        <div id="toggleColsXIcon" style="font-size:1.5em;"></div>
                    </button>
                    <div id="groupedColumnCheckboxes" class="grafica-modal-checkbox-list draggable-content" draggable="true">
                        <div style="position:absolute;right:8px;top:6px;z-index:4;">
                            <button class="grafica-close-btn" data-target="colsX" title="Cerrar sección">✕</button>
                        </div>
                    </div>
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
            const icon = document.getElementById('toggleRowsYIcon');
            if (isCollapsed('rowsY')) {
                rowsYDiv.style.display = 'none';
                if (icon) icon.innerHTML = '<i class="bi bi-arrows-expand"></i>';
                btnRowsY.setAttribute('aria-expanded', 'false');
                // visual: not active when collapsed
                btnRowsY.classList.remove('saved');
            } else {
                rowsYDiv.style.display = '';
                if (icon) icon.innerHTML = '<i class="bi bi-arrows-collapse"></i>';
                btnRowsY.setAttribute('aria-expanded', 'true');
                // visual: mark as active/saved when expanded
                btnRowsY.classList.add('saved');
            }
        }
        // Click toggles collapse and shows a temporary "pressed" (red) state
        btnRowsY.addEventListener('click', function() {
            btnRowsY.classList.add('pressed');
            setTimeout(() => btnRowsY.classList.remove('pressed'), 220);
            setCollapsed('rowsY', !isCollapsed('rowsY'));
            updateRowsYCollapse();
        });
        ['mousedown','mouseup','mouseleave','touchstart','touchend'].forEach(ev => {
            if (ev === 'mousedown' || ev === 'touchstart') {
                btnRowsY.addEventListener(ev, () => btnRowsY.classList.add('pressed'));
            } else {
                btnRowsY.addEventListener(ev, () => btnRowsY.classList.remove('pressed'));
            }
        });
        updateRowsYCollapse();
        // Eje X
        const colsXDiv = document.getElementById('groupedColumnCheckboxes');
        const btnColsX = document.getElementById('toggleColsX');
        function updateColsXCollapse() {
            const icon = document.getElementById('toggleColsXIcon');
            if (isCollapsed('colsX')) {
                colsXDiv.style.display = 'none';
                if (icon) icon.innerHTML = '<i class="bi bi-arrows-expand"></i>';
                btnColsX.setAttribute('aria-expanded', 'false');
                btnColsX.classList.remove('saved');
            } else {
                colsXDiv.style.display = '';
                if (icon) icon.innerHTML = '<i class="bi bi-arrows-collapse"></i>';
                btnColsX.setAttribute('aria-expanded', 'true');
                btnColsX.classList.add('saved');
            }
        }
        btnColsX.addEventListener('click', function() {
            btnColsX.classList.add('pressed');
            setTimeout(() => btnColsX.classList.remove('pressed'), 220);
            setCollapsed('colsX', !isCollapsed('colsX'));
            updateColsXCollapse();
        });
        ['mousedown','mouseup','mouseleave','touchstart','touchend'].forEach(ev => {
            if (ev === 'mousedown' || ev === 'touchstart') {
                btnColsX.addEventListener(ev, () => btnColsX.classList.add('pressed'));
            } else {
                btnColsX.addEventListener(ev, () => btnColsX.classList.remove('pressed'));
            }
        });
        updateColsXCollapse();

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
        groupedColumnCheckboxes.innerHTML = GroupColsX.map((group, gIdx) => {
            const onlyOneAndEqual = group.cols.length === 1 && group.group === group.cols[0];
            return `
                <div class="mb-2 border rounded p-2">
                    <div>
                        <input type="checkbox" class="form-check-input group-checkbox" id="group-${gIdx}">
                        <label class="form-check-label fw-bold" for="group-${gIdx}">${group.group}</label>
                    </div>
                    <div class="ms-3">
                        ${group.cols.map((col, cIdx) => {
                            if (onlyOneAndEqual) {
                                // Renderizar el checkbox hijo pero ocultarlo visualmente
                                return `
                                    <div class="only-child-duplicate">
                                        <input type="checkbox" class="form-check-input column-checkbox group-${gIdx}" 
                                               id="col-${gIdx}-${cIdx}" 
                                               value="${col}" 
                                               data-group="${group.group}"
                                               data-full-label="${col}"
                                               checked>
                                        <label class="form-check-label" for="col-${gIdx}-${cIdx}">${col}</label>
                                    </div>
                                `;
                            } else if (group.group === col) {
                                // Si hay más de un hijo y es igual, no mostrar nada (solo encabezado)
                                return '';
                            } else {
                                // Si son diferentes, mostrar solo el nombre de la columna
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

        // --- CLOSE BUTTONS: Simular click en el toggle correspondiente ---
        document.querySelectorAll('.grafica-close-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const target = btn.getAttribute('data-target');
                if (target === 'rowsY') {
                    const t = document.getElementById('toggleRowsY');
                    if (t) t.click();
                } else if (target === 'colsX') {
                    const t = document.getElementById('toggleColsX');
                    if (t) t.click();
                }
            });
        });

        // --- DRAG & DROP: Panels (swap columns) and inner contents (swap lists) ---
        (function setupDragAndDrop() {
            // Panels swap
            let draggedPanel = null;
            document.querySelectorAll('.draggable-panel').forEach(panel => {
                panel.addEventListener('dragstart', (e) => {
                    draggedPanel = panel;
                    panel.classList.add('dragging');
                    try { e.dataTransfer.setData('text/plain', panel.dataset.panel || ''); } catch (err) {}
                    e.dataTransfer.effectAllowed = 'move';
                });
                panel.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    e.dataTransfer.dropEffect = 'move';
                });
                panel.addEventListener('drop', (e) => {
                    e.preventDefault();
                    if (!draggedPanel || draggedPanel === panel) return;
                    const parent = panel.parentNode;
                    // Swap positions: insert dragged before the drop target
                    parent.insertBefore(draggedPanel, panel);
                });
                panel.addEventListener('dragend', () => {
                    panel.classList.remove('dragging');
                    draggedPanel = null;
                });
            });

            // Inner lists swap (rowsYCheckboxes <-> groupedColumnCheckboxes)
            let draggedContent = null;
            document.querySelectorAll('.draggable-content').forEach(content => {
                content.addEventListener('dragstart', (e) => {
                    draggedContent = content;
                    content.classList.add('dragging');
                    try { e.dataTransfer.setData('text/plain', content.id || ''); } catch (err) {}
                    e.dataTransfer.effectAllowed = 'move';
                });
                content.addEventListener('dragover', (e) => { e.preventDefault(); });
                content.addEventListener('drop', (e) => {
                    e.preventDefault();
                    if (!draggedContent || draggedContent === content) return;
                    // Swap the nodes themselves to preserve inner event listeners
                    const a = draggedContent;
                    const b = content;
                    const aParent = a.parentNode;
                    const bParent = b.parentNode;
                    const aNext = a.nextSibling === b ? a : a.nextSibling;
                    // Perform swap by replacing
                    aParent.replaceChild(b, a);
                    bParent.insertBefore(a, aNext);

                    // After swapping, re-attach close button handlers for safety
                    document.querySelectorAll('.grafica-close-btn').forEach(btn => {
                        btn.replaceWith(btn.cloneNode(true));
                    });
                    document.querySelectorAll('.grafica-close-btn').forEach(btn => {
                        btn.addEventListener('click', () => {
                            const target = btn.getAttribute('data-target');
                            if (target === 'rowsY') {
                                const t = document.getElementById('toggleRowsY');
                                if (t) t.click();
                            } else if (target === 'colsX') {
                                const t = document.getElementById('toggleColsX');
                                if (t) t.click();
                            }
                        });
                    });
                });
                content.addEventListener('dragend', () => {
                    content.classList.remove('dragging');
                    draggedContent = null;
                });
            });
        })();
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
