/**
 * GraficaModalEngine - Motor para visualizar gráficas en modales
 * @class
 */
class GraficaModalEngine {
    /**
     * Renderiza una gráfica en el contenedor dado.
     * Puedes adaptar esto para usar tus propios datos o motor de gráficas.
     */
    async renderGraficaInContainer(containerId, excelUrl, fileName) {
        const container = document.getElementById(containerId);
        if (!container) return;

        // Mostrar estado de carga
        container.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-success" role="status"></div>
                <p class="mt-3">Procesando gráfica...</p>
            </div>
        `;

        // Ejemplo: cargar Chart.js si no está presente
        if (typeof Chart === 'undefined') {
            await this.loadChartJs();
        }

        // Aquí deberías procesar el archivo Excel y extraer los datos para la gráfica.
        // Por simplicidad, se muestra una gráfica de ejemplo.
        setTimeout(() => {
            container.innerHTML = `<canvas id="chart-${containerId}" height="320"></canvas>`;
            const ctx = document.getElementById(`chart-${containerId}`).getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['A', 'B', 'C', 'D'],
                    datasets: [{
                        label: 'Ejemplo',
                        data: [12, 19, 3, 5],
                        backgroundColor: [
                            'rgba(54, 162, 235, 0.7)',
                            'rgba(255, 206, 86, 0.7)',
                            'rgba(75, 192, 192, 0.7)',
                            'rgba(255, 99, 132, 0.7)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: true }
                    }
                }
            });
        }, 600); // Simula carga
    }

    /**
     * Carga Chart.js dinámicamente si no está presente
     */
    loadChartJs() {
        return new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
            script.onload = resolve;
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }
}

// Instancia global
window.GraficaModalEngine = new GraficaModalEngine();