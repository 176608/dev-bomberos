<div class="card shadow-sm">
    <div class="card-body">
        <h2 class="text-success mb-4">
            <i class="bi bi-map me-2"></i>Cartografía
        </h2>
        
        <div id="mapas-container">
            <div class="text-center py-3">
                <i class="bi bi-hourglass-split"></i>
                <p>Cargando mapas...</p>
            </div>
        </div>
    </div>
</div>

<style>
/* Estilos específicos para cartografía */
.mapa-row {
    margin-bottom: 30px;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.mapa-row:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    transform: translateY(-2px);
}

.mapa-header {
    background: linear-gradient(135deg, #2a6e48 0%, #66d193 100%);
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
}

.mapa-title {
    color: white;
    font-weight: bold;
    font-size: 1.2em;
    margin: 0;
    flex: 1;
}

.mapa-seccion {
    color: #ffd700;
    font-size: 0.9em;
    margin: 0;
    margin-top: 5px;
}

.mapa-btn {
    background-color: #ffd700;
    color: #2a6e48;
    border: none;
    padding: 8px 16px;
    border-radius: 5px;
    font-weight: bold;
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.mapa-btn:hover {
    background-color: #ffed4e;
    color: #1e4d35;
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
}

.mapa-iframe-container {
    position: relative;
    width: 100%;
    height: 400px;
    background-color: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
}

.mapa-iframe {
    width: 100%;
    height: 100%;
    border: none;
    background-color: white;
}

.mapa-placeholder {
    text-align: center;
    color: #6c757d;
    font-size: 1.1em;
}

.mapa-placeholder i {
    font-size: 2em;
    margin-bottom: 10px;
    display: block;
}

.mapa-descripcion {
    padding: 20px;
    background-color: white;
    border-top: 1px solid #e0e0e0;
}

.mapa-descripcion h5 {
    color: #2a6e48;
    margin-bottom: 15px;
    font-weight: bold;
}

.mapa-descripcion p {
    color: #495057;
    line-height: 1.6;
    margin-bottom: 0;
    text-align: justify;
}

/* Responsive */
@media (max-width: 768px) {
    .mapa-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .mapa-btn {
        align-self: flex-end;
    }
    
    .mapa-iframe-container {
        height: 300px;
    }
    
    .mapa-descripcion {
        padding: 15px;
    }
}

@media (max-width: 576px) {
    .mapa-iframe-container {
        height: 250px;
    }
    
    .mapa-title {
        font-size: 1.1em;
    }
    
    .mapa-seccion {
        font-size: 0.85em;
    }
}
</style>