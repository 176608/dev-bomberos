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
    /*background: inherit;*/
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
    bottom: 0;
    left: 0;
    right: 0;
    color: #fff;
    background: rgba(0, 0, 0, 0);
    padding: 0.5rem;
    transform: translateY(100%);
    transition: all 0.3s ease;
}

.hover-content {
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    background: rgba(0, 0, 0, 1);
    font-size: 0.85rem;
    font-weight: 500;
}

.hover-text {
    text-shadow: 0 1px 3px rgba(0,0,0,0.3);
}

/* Efectos hover */
.enlace-completo:hover {
    background: rgba(0, 0, 0, 1);
}

.enlaceTema:hover {
    transform: translateY(-6px) scale(1.02);
    filter: brightness(0.9) saturate(1.1);
    background: rgba(0, 0, 0, 1);
}

.enlaceTema:hover .row-hover {
    transform: translateY(0);
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
        font-size: 0.8rem;
    }
    
    .enlace-completo {
        padding: 1rem 0.5rem !important;
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