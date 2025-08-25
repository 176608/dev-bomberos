<div class="card shadow-sm">
    <div class="card-body p-0">
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
        <img src="{{ asset('imagenes/iconoesta2.png') }}" alt="Icono Estadística" class="img-fluid" style="max-height: 80px;" 
             onerror="this.src='{{ asset('img/icons/chart-icon.png') }}'; this.onerror=null;">
    </div>
</div>

        <div class="row g-0 min-vh-75">
            <div class="col-12">
                <div class="p-4">
                    <h4 class="mb-4">
                        <i class="bi bi-list-task me-2"></i>Selecciona un tema para explorar:
                    </h4>

                    @if(!isset($temas) || $temas->isEmpty())
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            No se pudieron cargar los temas estadísticos. Por favor, 
                            <a href="{{ url('/sigem?section=estadistica') }}" class="alert-link">haz clic aquí para intentar nuevamente</a>.
                        </div>

                        <script>
                            setTimeout(function() {
                                window.location.href = '{{ route("sigem.laravel.partial", ["section" => "estadistica"]) }}';
                            }, 3000);
                        </script>
                    @else
                        <div class="temas-grid-container">
                            @foreach($temas as $index => $tema)
                                @php 
                                    $coloresEstilo = [
                                        'background-color: #8FBC8F; color: #3b3b3bff;',
                                        'background-color: #87CEEB; color: #3b3b3bff;',
                                        'background-color: #DDA0DD; color: #3b3b3bff;',
                                        'background-color: #F0E68C; color: #3b3b3bff;',
                                        'background-color: #FFA07A; color: #3b3b3bff;',
                                        'background-color: #98FB98; color: #3b3b3bff;'
                                    ];
                                    $colorTema = $coloresEstilo[$index % count($coloresEstilo)];
                                @endphp
                                <div class="tema-card" style="{{ $colorTema }}">
                                    <a href="{{ route('sigem.estadistica.tema', ['tema_id' => $tema->tema_id]) }}" 
                                       class="enlace-completo" 
                                       data-tema-id="{{ $tema->tema_id }}">

                                        <div class="row-icono">
                                            @if($tema->orden_indice == 1)
                                                <i class="bi bi-globe"></i>
                                            @elseif($tema->orden_indice == 2)
                                                <i class="bi bi-leaf"></i>
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

                                        <div class="row-titulo">
                                            <h5 class="titulo-tema mb-3">
                                                {{ $tema->orden_indice }}. {{ $tema->tema_titulo }}
                                            </h5>
                                        </div>

                                        <div class="row-hover">
                                            <div class="hover-content">
                                                <i class="bi bi-arrow-right-circle-fill"></i>
                                                <span class="hover-text">
                                                    &nbsp; Explorar {{ $tema->subtemas ? $tema->subtemas->count() : 0 }} subtemas
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
.temas-grid-container {
    display: grid;
    grid-template-columns: repeat(3, 1fr); 
    gap: 1.5rem; 
    padding: 0.5rem;
    width: 100%;
}

.tema-card {
    border-radius: 16px;
    min-height: 110px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    position: relative;
    overflow: hidden;
    border: none;
    display: flex; 
}

/
.enlace-completo {
    display: flex !important;
    flex-direction: column !important;
    justify-content: space-between !important;
    align-items: center !important;
    height: 100% !important;
    width: 100% !important;
    color: #3b3b3bff !important;
    text-decoration: none !important;
    padding: 1rem 0.75rem !important;
    position: relative;
    z-index: 1;
}

.row-icono {
    display: flex;
    justify-content: center;
    align-items: center;
    flex: 1;
    margin-bottom: 0.3rem;
}

.row-icono i.bi,
.row-icono {
    color: #3b3b3bff !important;
    font-size: 2rem;
    transition: all 0.3s ease;
}

.row-titulo {
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
    margin-bottom: auto;
}

.titulo-tema {
    color: #3b3b3bff !important;
    font-size: 0.95rem;
    font-weight: 700;
    text-shadow: 0 2px 8px rgba(0,0,0,0.18);
    margin: 0;
    letter-spacing: 0.01em;
    line-height: 1.2;
}

.row-hover {
    position: absolute;
    bottom: 0.2rem;
    right: 0.6rem;
    left: auto;
    color: #fff;
    background: rgba(0, 0, 0, 0);
    padding: 0.25rem 0.2rem;
    border-radius: 6px;
    transform: translateY(10px) translateX(10px);
    opacity: 0;
    transition: all 0.3s ease;
}

.hover-content {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    color: #fff;
    background: transparent;
    font-size: 0.65rem;
    font-weight: 500;
}

.hover-text {
    text-shadow: 0 1px 3px rgba(0,0,0,0.3);
}

.tema-card:hover {
    transform: translateY(-4px) scale(1.01);
    filter: brightness(0.9) saturate(1.1);
    background-color: rgba(0, 0, 0, 0.85) !important;
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
}

.tema-card:hover .enlace-completo {
    background-color: #15412cff !important;
}

.tema-card:hover .row-hover {
    transform: translateY(0) translateX(0);
    opacity: 1;
}

.tema-card:hover .row-icono i.bi,
.tema-card:hover .row-icono {
    transform: scale(1.05);
    filter: drop-shadow(0 4px 8px rgba(0,0,0,0.2)) brightness(0) saturate(100%) invert(100%);
}

.tema-card:hover .titulo-tema {
    color: #ffffffff !important;
    transform: translateY(-3px);
}

.tema-card:active {
    transform: translateY(-1px) scale(0.995);
    box-shadow: 0 6px 20px rgba(0,0,0,0.12);
}

@media (max-width: 991px) {
    .temas-grid-container {
        grid-template-columns: repeat(2, 1fr); /* 2 columnas en tablet */
        gap: 1.2rem;
    }
}

@media (max-width: 767px) {
    .temas-grid-container {
        grid-template-columns: 1fr; /* 1 columna en móvil */
        gap: 1rem;
        padding: 0.25rem;
    }
    
    .tema-card {
        min-height: 90px;
    }
    
    .row-icono i.bi,
    .row-icono {
        color: #3b3b3bff !important;
        font-size: 1.6rem;
    }
    
    .titulo-tema {
        font-size: 0.85rem;
    }
    
    .enlace-completo {
        padding: 0.75rem 0.5rem !important;
    }
}

@media (max-width: 576px) {
    .row-icono i.bi,
    .row-icono {
        font-size: 1.4rem;
    }
    
    .titulo-tema {
        font-size: 0.8rem;
    }
    
    .tema-card {
        min-height: 80px;
    }
}
</style>