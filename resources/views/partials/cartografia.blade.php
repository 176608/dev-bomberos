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

.mapa-btn:not([href]) {
    background-color: #6c757d;
    cursor: not-allowed;
    opacity: 0.6;
}

/* NUEVO: Contenedor de imagen y descripción */
.mapa-content {
    display: flex;
    min-height: 200px;
    background-color: white;
}

.mapa-image-container {
    flex: 0 0 50%;
    position: relative;
    overflow: hidden;
    background: linear-gradient(45deg, #f8f9fa 0%, #e9ecef 100%);
    display: flex;
    align-items: center;
    justify-content: center;
}

.mapa-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: all 0.4s ease;
    cursor: pointer;
}

.mapa-image:hover {
    transform: scale(1.05);
    filter: brightness(1.1) contrast(1.1);
}

.mapa-image-placeholder {
    text-align: center;
    color: #6c757d;
    padding: 40px 20px;
}

.mapa-image-placeholder i {
    font-size: 3em;
    margin-bottom: 15px;
    display: block;
    color: #2a6e48;
}

.mapa-image-placeholder h5 {
    color: #2a6e48;
    margin-bottom: 10px;
    font-weight: bold;
}

.mapa-image-placeholder p {
    margin: 0;
    font-size: 0.9em;
}

.mapa-descripcion {
    flex: 1;
    padding: 20px;
    background-color: white;
    border-left: 1px solid #e0e0e0;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.mapa-descripcion h5 {
    color: #2a6e48;
    margin-bottom: 15px;
    font-weight: bold;
    display: flex;
    align-items: center;
    gap: 8px;
}

.mapa-descripcion p {
    color: #495057;
    line-height: 1.6;
    margin-bottom: 0;
    text-align: justify;
}

/* Overlay de hover en imagen */
.mapa-image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(42, 110, 72, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: all 0.3s ease;
    color: white;
    font-size: 1.2em;
    font-weight: bold;
}

.mapa-image-container:hover .mapa-image-overlay {
    opacity: 1;
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
    
    .mapa-content {
        flex-direction: column;
        min-height: auto;
    }
    
    .mapa-image-container {
        flex: none;
        height: 200px;
    }
    
    .mapa-descripcion {
        border-left: none;
        border-top: 1px solid #e0e0e0;
        padding: 15px;
    }
}

@media (max-width: 576px) {
    .mapa-image-container {
        height: 150px;
    }
    
    .mapa-image-placeholder {
        padding: 20px 15px;
    }
    
    .mapa-image-placeholder i {
        font-size: 2em;
        margin-bottom: 10px;
    }
    
    .mapa-image-placeholder h5 {
        font-size: 1em;
        margin-bottom: 5px;
    }
    
    .mapa-image-placeholder p {
        font-size: 0.8em;
    }
    
    .mapa-title {
        font-size: 1.1em;
    }
    
    .mapa-seccion {
        font-size: 0.85em;
    }
    
    .mapa-descripcion {
        padding: 15px;
    }
}
</style>