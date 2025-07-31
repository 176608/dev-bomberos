<div class="card shadow-sm">
    <div class="card-body p-0">
        <!-- Header del Catálogo -->
        <div class="catalogo-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <h2 class="text-success mb-3 text-center">
                            <i class="bi bi-journal-text"></i> Catálogo de Cuadros Estadísticos
                        </h2>
                        
                        <div class="alert alert-info mb-3">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Sistema de clasificación:</strong> Los cuadros se identifican mediante una clave conformada por el número de tema, identificador del subtema y número de cuadro.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Layout Principal: Sidebar + Content -->
        <div class="catalogo-main-layout">
            <!-- SIDEBAR: Índice de Navegación -->
            <div class="catalogo-sidebar">
                <div class="sidebar-header">
                    <h5>
                        <i class="bi bi-list-ul me-2"></i>
                        Índice de Navegación
                    </h5>
                    <div class="sidebar-stats">
                        <small class="text-muted">
                            <span id="stats-temas">0</span> temas · 
                            <span id="stats-subtemas">0</span> subtemas
                        </small>
                    </div>
                </div>
                
                <div class="sidebar-content">
                    <div id="indice-container">
                        <div class="loading-state">
                            <div class="loading-spinner"></div>
                            <p>Cargando índice...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MAIN CONTENT: Lista de Cuadros -->
            <div class="catalogo-main-content">
                <div class="content-header">
                    <h5>
                        <i class="bi bi-table me-2"></i>
                        Cuadros Estadísticos
                    </h5>
                    <div class="content-controls">
                        <button class="btn btn-sm btn-outline-primary" id="btn-expandir-todo">
                            <i class="bi bi-arrows-expand"></i>
                            Expandir Todo
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" id="btn-contraer-todo">
                            <i class="bi bi-arrows-collapse"></i>
                            Contraer Todo
                        </button>
                    </div>
                </div>
                
                <div class="content-body">
                    <div id="cuadros-container">
                        <div class="loading-state">
                            <div class="loading-spinner"></div>
                            <p>Cargando cuadros...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* === LAYOUT PRINCIPAL === */
.catalogo-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 20px;
    border-bottom: 1px solid #dee2e6;
}

.catalogo-main-layout {
    display: flex;
    min-height: 70vh;
    background-color: #fff;
}

/* === SIDEBAR ÍNDICE === */
.catalogo-sidebar {
    flex: 0 0 320px;
    background: linear-gradient(180deg, #f8f9fa 0%, #ffffff 100%);
    border-right: 1px solid #dee2e6;
    display: flex;
    flex-direction: column;
}

.sidebar-header {
    padding: 15px 20px;
    border-bottom: 1px solid #e9ecef;
    background-color: #2a6e48;
    color: white;
}

.sidebar-header h5 {
    margin: 0;
    font-size: 1rem;
    font-weight: 600;
}

.sidebar-stats {
    margin-top: 5px;
}

.sidebar-stats .text-muted {
    color: rgba(255, 255, 255, 0.8) !important;
    font-size: 0.85rem;
}

.sidebar-content {
    flex: 1;
    overflow-y: auto;
    padding: 10px;
}

/* === MAIN CONTENT === */
.catalogo-main-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-width: 0; /* Para que flex funcione bien */
}

.content-header {
    padding: 15px 20px;
    border-bottom: 1px solid #e9ecef;
    background-color: #fff;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: sticky;
    top: 0;
    z-index: 10;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.content-header h5 {
    margin: 0;
    color: #2a6e48;
    font-weight: 600;
}

.content-controls {
    display: flex;
    gap: 8px;
}

.content-body {
    flex: 1;
    overflow-y: auto;
    padding: 0;
}

/* === ESTILOS DEL ÍNDICE === */
.tema-item {
    margin-bottom: 8px;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
}

.tema-item:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transform: translateY(-1px);
}

.tema-header {
    padding: 12px 15px;
    cursor: pointer;
    font-weight: 600;
    font-size: 0.9rem;
    color: white;
    text-align: center;
    position: relative;
    transition: all 0.3s ease;
}

.tema-header:hover {
    filter: brightness(1.1);
}

.tema-header.active {
    box-shadow: inset 0 0 0 2px rgba(255, 255, 255, 0.5);
}

.subtemas-list {
    background: white;
    border-top: 1px solid rgba(255, 255, 255, 0.2);
}

.subtema-item {
    display: flex;
    align-items: center;
    padding: 8px 12px;
    cursor: pointer;
    font-size: 0.85rem;
    border-bottom: 1px solid #f8f9fa;
    transition: all 0.2s ease;
}

.subtema-item:hover {
    background: linear-gradient(90deg, #e8f5e8 0%, #f0f8f0 100%);
    padding-left: 16px;
}

.subtema-item.active {
    background: linear-gradient(90deg, #d4edda 0%, #e8f5e8 100%);
    border-left: 3px solid #2a6e48;
    font-weight: 600;
}

.subtema-codigo {
    flex: 0 0 50px;
    font-weight: 600;
    color: #2a6e48;
    text-align: center;
    border-right: 1px solid #e9ecef;
    margin-right: 10px;
    padding-right: 10px;
    font-size: 0.8rem;
}

.subtema-titulo {
    flex: 1;
    color: #495057;
    line-height: 1.3;
}

/* === LOADING STATES === */
.loading-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 40px 20px;
    color: #6c757d;
}

.loading-spinner {
    width: 24px;
    height: 24px;
    border: 3px solid #f3f3f3;
    border-top: 3px solid #2a6e48;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-bottom: 15px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* === EFECTOS DE FOCUS === */
.highlight-focus {
    background: linear-gradient(90deg, #fff3cd 0%, #ffeaa7 100%) !important;
    border: 2px solid #ffc107 !important;
    box-shadow: 0 0 15px rgba(255, 193, 7, 0.5) !important;
    animation: pulseHighlight 1s ease-in-out;
}

@keyframes pulseHighlight {
    0% { transform: scale(1); }
    50% { transform: scale(1.02); }
    100% { transform: scale(1); }
}

/* === RESPONSIVE === */
@media (max-width: 1024px) {
    .catalogo-sidebar {
        flex: 0 0 280px;
    }
    
    .content-header {
        flex-direction: column;
        gap: 10px;
        align-items: flex-start;
    }
    
    .content-controls {
        width: 100%;
        justify-content: flex-end;
    }
}

@media (max-width: 768px) {
    .catalogo-main-layout {
        flex-direction: column;
    }
    
    .catalogo-sidebar {
        flex: none;
        max-height: 300px;
        border-right: none;
        border-bottom: 1px solid #dee2e6;
    }
    
    .sidebar-content {
        max-height: 250px;
    }
    
    .tema-header {
        font-size: 0.85rem;
        padding: 10px 12px;
    }
    
    .subtema-item {
        font-size: 0.8rem;
        padding: 6px 10px;
    }
    
    .content-header {
        position: static;
    }
}

@media (max-width: 576px) {
    .catalogo-header {
        padding: 15px;
    }
    
    .catalogo-header h2 {
        font-size: 1.3rem;
    }
    
    .catalogo-header .alert {
        font-size: 0.85rem;
        padding: 10px;
    }
    
    .content-controls .btn {
        font-size: 0.8rem;
        padding: 4px 8px;
    }
    
    .content-controls .btn i {
        font-size: 0.8rem;
    }
}

/* === COLORES PARA TEMAS === */
.tema-1 { background: linear-gradient(135deg, #8FBC8F 0%, #7AA87A 100%); }
.tema-2 { background: linear-gradient(135deg, #87CEEB 0%, #6BB6E6 100%); }
.tema-3 { background: linear-gradient(135deg, #DDA0DD 0%, #D280D2 100%); }
.tema-4 { background: linear-gradient(135deg, #F0E68C 0%, #EDD76B 100%); }
.tema-5 { background: linear-gradient(135deg, #FFA07A 0%, #FF8A5B 100%); }
.tema-6 { background: linear-gradient(135deg, #98FB98 0%, #7FE57F 100%); }

/* === ESTILOS PARA CUADROS === */
.tema-cuadros {
    margin-bottom: 30px;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.tema-cuadros-header {
    background: linear-gradient(135deg, #2a6e48 0%, #66d193 100%);
    padding: 15px 20px;
    color: white;
}

.tema-cuadros-header h3 {
    margin: 0;
    font-size: 1.3rem;
    font-weight: 600;
}

.tema-numero {
    font-weight: 700;
    margin-right: 10px;
}

.subtema-cuadros {
    border-bottom: 1px solid #e9ecef;
}

.subtema-cuadros:last-child {
    border-bottom: none;
}

.subtema-cuadros-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 12px 20px;
    border-bottom: 1px solid #dee2e6;
}

.subtema-cuadros-header h4 {
    margin: 0;
    font-size: 1.1rem;
    color: #2a6e48;
    display: flex;
    align-items: center;
    gap: 15px;
}

.subtema-codigo {
    background: #2a6e48;
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.9rem;
    font-weight: 600;
    min-width: 50px;
    text-align: center;
}

.cuadros-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 15px;
    padding: 20px;
    background: white;
}

.cuadro-item {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 15px;
    cursor: pointer;
    transition: all 0.3s ease;
    background: white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.cuadro-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    border-color: #2a6e48;
}

.cuadro-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
    padding-bottom: 8px;
    border-bottom: 1px solid #f1f3f4;
}

.cuadro-codigo {
    background: linear-gradient(135deg, #2a6e48 0%, #66d193 100%);
    color: white;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 0.85rem;
    font-weight: 600;
    letter-spacing: 0.5px;
}

.cuadro-icon {
    color: #6c757d;
    font-size: 1.1rem;
    transition: all 0.3s ease;
}

.cuadro-item:hover .cuadro-icon {
    color: #2a6e48;
    transform: scale(1.2);
}

.cuadro-body {
    margin-bottom: 12px;
}

.cuadro-titulo {
    color: #2a6e48;
    font-size: 0.95rem;
    font-weight: 600;
    line-height: 1.3;
    margin-bottom: 8px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.cuadro-descripcion {
    color: #6c757d;
    font-size: 0.85rem;
    line-height: 1.4;
    margin: 0;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.cuadro-footer {
    padding-top: 8px;
    border-top: 1px solid #f1f3f4;
}

.cuadro-footer small {
    font-size: 0.75rem;
    display: flex;
    align-items: center;
    gap: 5px;
}

/* === RESPONSIVE PARA CUADROS === */
@media (max-width: 768px) {
    .cuadros-grid {
        grid-template-columns: 1fr;
        gap: 12px;
        padding: 15px;
    }
    
    .cuadro-item {
        padding: 12px;
    }
    
    .tema-cuadros-header {
        padding: 12px 15px;
    }
    
    .tema-cuadros-header h3 {
        font-size: 1.1rem;
    }
    
    .subtema-cuadros-header {
        padding: 10px 15px;
    }
    
    .subtema-cuadros-header h4 {
        font-size: 1rem;
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }
}

@media (max-width: 576px) {
    .cuadros-grid {
        padding: 10px;
    }
    
    .cuadro-titulo {
        font-size: 0.9rem;
    }
    
    .cuadro-descripcion {
        font-size: 0.8rem;
    }
    
    .subtema-codigo {
        font-size: 0.8rem;
        padding: 3px 6px;
    }
}
</style>