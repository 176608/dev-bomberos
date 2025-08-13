<div class="card shadow-sm">
    <div class="card-body p-0">
        <!-- Cabecera --> 
<div class="row g-0 border-bottom">
    <div class="col-8">
        <div class="p-4">
            <h2 class="text-success mb-2">
                Sección Estadística
            </h2>
            <p class="text-muted mb-0">Consultas de información estadística relevante y precisa</p>
        </div>
    </div>
    <div class="col-4 d-flex align-items-center justify-content-center bg-light">
        <!-- Corregir la ruta de la imagen -->
        <img src="{{ asset('imagenes/iconoesta2.png') }}" alt="Icono Estadística" class="img-fluid" style="max-height: 80px;" 
             onerror="this.src='{{ asset('img/icons/chart-icon.png') }}'; this.onerror=null;">
    </div>
</div>


        <!-- Contenido principal -->
        <div class="row g-0 min-vh-75">
            <!-- Área de temas -->
            <div class="col-12">
                <div class="p-4">
                    <h4 class="mb-4">
                        <i class="bi bi-list-task me-2"></i>Selecciona un tema para explorar:
                    </h4>

                    @if(!isset($temas) || $temas->isEmpty())
                        <!-- Manejo de error cuando $temas no está definida o está vacía -->
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            No se pudieron cargar los temas estadísticos. Por favor, 
                            <a href="{{ url('/sigem?section=estadistica') }}" class="alert-link">haz clic aquí para intentar nuevamente</a>.
                        </div>

                        <!-- Agregar un script para recargar la página después de un breve retraso -->
                        <script>
                            setTimeout(function() {
                                window.location.href = '{{ route("sigem.laravel.partial", ["section" => "estadistica"]) }}';
                            }, 3000);
                        </script>
                    @else
                        <!-- El contenido normal cuando $temas está definida -->
                        <div class="row" id="temas-grid">
                            @foreach($temas as $index => $tema)
                                @php 
                                    $coloresEstilo = [
                                        'background-color: #8FBC8F; color: black;',
                                        'background-color: #87CEEB; color: black;',
                                        'background-color: #DDA0DD; color: black;',
                                        'background-color: #F0E68C; color: black;',
                                        'background-color: #FFA07A; color: black;',
                                        'background-color: #98FB98; color: black;'
                                    ];
                                    $colorTema = $coloresEstilo[$index % count($coloresEstilo)];
                                @endphp
                                <div class="col-lg-4 col-md-6 mb-4 enlaceTema" style="{{ $colorTema }}">
                                    <a href="{{ route('sigem.estadistica.tema', ['tema_id' => $tema->tema_id]) }}" 
                                       class="enlace-completo" 
                                       data-tema-id="{{ $tema->tema_id }}">

                                        <!-- Fila 1: Icono -->
                                        <div class="row-icono">
                                            @if($tema->orden_indice == 1)
                                                <i class="bi bi-globe"></i>
                                            @elseif($tema->orden_indice == 2)
                                                <img src="{{ asset('img/iconsBT/leaf-fill.svg') }}" 
                                                     alt="Medio Ambiente" 
                                                     class="svg-icon"
                                                     onerror="this.outerHTML='<i class=\'bi bi-leaf\'></i>';">
                                            @elseif($tema->orden_indice == 3)
                                                <i class="bi bi-person-bounding-box"></i>
                                            @elseif($tema->orden_indice == 4)
                                                <i class="bi bi-archive-fill"></i>
                                            @elseif($tema->orden_indice == 5)
                                                <i class="bi bi-cash-coin"></i>
                                            @elseif($tema->orden_indice == 6)
                                                <i class="bi bi-bank2"></i>
                                            @else
                                                <i class="bi bi-file-earmark-text"></i>
                                            @endif
                                        </div>

                                        <!-- Fila 2: Título -->
                                        <div class="row-titulo">
                                            <h5 class="titulo-tema">
                                                {{ $tema->orden_indice }}. {{ $tema->tema_titulo }}
                                            </h5>
                                        </div>

                                        <!-- Fila 3: Hover info (solo visible en hover) -->
                                        <div class="row-hover">
                                            <div class="hover-content">
                                                <i class="bi bi-cursor-fill me-2"></i>
                                                <span class="hover-text">
                                                    Explorar {{ $tema->subtemas ? $tema->subtemas->count() : 0 }} subtemas disponibles
                                                </span>
                                            </div>
                                        </div>

                                    </a>

                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* CAMBIO PRINCIPAL: El enlace debe llenar todo el contenedor */
.enlace-completo {
    display: flex !important;
    flex-direction: column !important;
    justify-content: space-between !important;
    align-items: center !important;
    height: 100% !important;
    width: 100% !important;
    color: inherit !important;
    text-decoration: none !important;
    padding: 1.5rem 1rem !important;
    position: relative;
    z-index: 1;
    margin: 0 0.5rem; /* Margen interno en lugar de externo */
}

.enlaceTema {
    border-radius: 12px;
    min-height: 140px;
    display: flex;
    align-items: stretch;
    transition: all 0.3s ease;
    box-shadow: 0 2px 10px rgba(0,0,0,0.07);
    position: relative;
    overflow: hidden;
    border: none;
    margin-bottom: 2rem; /* Solo margen inferior para separar filas */
}

/* Fila del icono */
.row-icono {
    display: flex;
    justify-content: center;
    align-items: center;
    flex: 1;
    margin-bottom: 0.5rem;
}

.row-icono i.bi,
.row-icono .svg-icon {
    font-size: 2.5rem;
    transition: all 0.3s ease;
}

.svg-icon {
    width: 2.5rem;
    height: 2.5rem;
}

/* Fila del título */
.row-titulo {
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
    margin-bottom: auto;
}

.titulo-tema {
    color: #000000ff !important;
    font-size: 1.1rem;
    font-weight: 700;
    text-shadow: 0 2px 8px rgba(0,0,0,0.18);
    margin: 0;
    letter-spacing: 0.01em;
    line-height: 1.3;
}

/* Fila hover (inicialmente oculta) */
.row-hover {
    position: absolute;
    bottom: 0.5rem; /* AJUSTE: Separar un poco del borde inferior */
    right: 0.75rem; /* AJUSTE: Posicionar en esquina derecha */
    left: auto; /* AJUSTE: Quitar el left:0 para que esté solo en la derecha */
    color: #fff;
    background: rgba(0, 0, 0, 0.8); /* AJUSTE: Fondo más visible */
    padding: 0.3rem 0.5rem; /* AJUSTE: Padding más pequeño */
    border-radius: 4px; /* NUEVO: Bordes redondeados pequeños para el texto hover */
    transform: translateY(20px) translateX(20px); /* AJUSTE: Ocultar fuera de vista */
    opacity: 0; /* AJUSTE: Transparencia inicial */
    transition: all 0.3s ease;
}

.hover-content {
    display: flex;
    align-items: center;
    justify-content: flex-end; /* AJUSTE: Alinear contenido a la derecha */
    color: #fff;
    background: transparent; /* AJUSTE: Quitar el fondo negro del contenido */
    font-size: 0.7rem; /* AJUSTE: Texto más pequeño */
    font-weight: 500;
}

.hover-text {
    text-shadow: 0 1px 3px rgba(0,0,0,0.3);
}

/* Efectos hover - AJUSTE: Aplicar hover a todo el div enlaceTema */
.enlaceTema:hover {
    transform: translateY(-6px) scale(1.02);
    filter: brightness(0.9) saturate(1.1);
    background-color: rgba(0, 0, 0, 0.85) !important; /* AJUSTE: Hover más sutil */
    box-shadow: 0 15px 40px rgba(0,0,0,0.2); /* AJUSTE: Sombra más pronunciada */
}

.enlaceTema:hover .enlace-completo {
    background-color: transparent !important; /* AJUSTE: No cambiar fondo del enlace */
}

.enlaceTema:hover .row-hover {
    transform: translateY(0) translateX(0); /* AJUSTE: Mostrar en posición final */
    opacity: 1; /* AJUSTE: Hacer visible */
}

.enlaceTema:hover .row-icono i.bi,
.enlaceTema:hover .row-icono .svg-icon {
    transform: scale(1.1);
    filter: drop-shadow(0 4px 8px rgba(0,0,0,0.2)) brightness(0) saturate(100%) invert(100%);
}

.enlaceTema:hover .titulo-tema {
    color: #ffffffff !important;
    transform: translateY(-5px);
}

/* Efecto activo */
.enlaceTema:active {
    transform: translateY(-2px) scale(0.99);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

/* Responsive */
@media (max-width: 768px) {
    .enlaceTema {
        min-height: 120px;
        margin: 0 0.5rem 1rem 0.5rem; /* AJUSTE: Menor separación en móvil */
    }
    
    .row-icono i.bi,
    .row-icono .svg-icon {
        font-size: 2rem;
    }
    
    .svg-icon {
        color: black;
        width: 2rem;
        height: 2rem;
    }
    
    .titulo-tema {
        font-size: 1rem;
    }
    
    .hover-content {
        font-size: 0.65rem; /* AJUSTE: Aún más pequeño en móvil */
    }
    
    .enlace-completo {
        padding: 1rem 0.5rem !important;
    }
    
    .row-hover {
        bottom: 0.3rem; /* AJUSTE: Más cerca del borde en móvil */
        right: 0.5rem; /* AJUSTE: Menos separación del borde */
        padding: 0.25rem 0.4rem; /* AJUSTE: Padding más pequeño */
    }
}

@media (max-width: 576px) {
    .row-icono i.bi,
    .row-icono .svg-icon {
        font-size: 1.8rem;
    }
    
    .titulo-tema {
        font-size: 0.9rem;
    }
}
</style>