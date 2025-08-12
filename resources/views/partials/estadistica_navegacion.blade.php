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
                                        'background-color: #F0E68C; color: white;',
                                        'background-color: #FFA07A; color: white;',
                                        'background-color: #98FB98; color: white;'
                                    ];
                                    $colorTema = $coloresEstilo[$index % count($coloresEstilo)];
                                @endphp
                                <div class="col-lg-4 col-md-6 mb-4 enlaceTema" style="{{ $colorTema }}">
                                    <a href="{{ route('sigem.estadistica.tema', ['tema_id' => $tema->tema_id]) }}" 
                                       class="h-100 text-decoration-none" 
                                       data-tema-id="{{ $tema->tema_id }}">

                                       <div class="row text-center pt-2 m-2">
                                            @if($tema->orden_indice == 1)
                                                <i class="bi bi-globe"></i>
                                            @elseif($tema->orden_indice == 2)
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-leaf-fill">
                                                    <path d="M1.4 1.7c.217.289.65.84 1.725 1.274 1.093.44 2.885.774 5.834.528 2.02-.168 3.431.51 4.326 1.556C14.161 6.082 14.5 7.41 14.5 8.5q0 .344-.027.734C13.387 8.252 11.877 7.76 10.39 7.5c-2.016-.288-4.188-.445-5.59-2.045-.142-.162-.402-.102-.379.112.108.985 1.104 1.82 1.844 2.308 2.37 1.566 5.772-.118 7.6 3.071.505.8 1.374 2.7 1.75 4.292.07.298-.066.611-.354.715a.7.7 0 0 1-.161.042 1 1 0 0 1-1.08-.794c-.13-.97-.396-1.913-.868-2.77C12.173 13.386 10.565 14 8 14c-1.854 0-3.32-.544-4.45-1.435-1.124-.887-1.889-2.095-2.39-3.383-1-2.562-1-5.536-.65-7.28L.73.806z"/>
                                                </svg>
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

                                        <div class="row text-center">
                                            <div class="col-12">
                                                <h5 class="mb-0 fw-bold">
                                                {{ $tema->orden_indice }}. {{ $tema->tema_titulo }}
                                                </h5>
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
/* Estilos necesarios */

.enlaceTema {
    border-radius: 12px;
    min-height: 140px;
    display: flex;
    align-items: stretch;
    transition: background 0.3s, box-shadow 0.3s, transform 0.2s;
    box-shadow: 0 2px 10px rgba(0,0,0,0.07);
    position: relative;
    overflow: hidden;
    border: none;
    background: inherit;
}

.enlaceTema a {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    height: 100%;
    text-decoration: none !important;
    padding: 0;
}

.enlaceTema .row.text-center.pt-2.m-2 {
    margin: 0 !important;
    padding-top: 1.5rem !important;
    padding-bottom: 0.5rem !important;
}

.enlaceTema i.bi {
    font-size: 2.5rem;
    filter: drop-shadow(0 2px 4px rgba(0,0,0,0.12));
    margin-bottom: 0.5rem;
    transition: color 0.2s;
}

.enlaceTema h5 {
    color: #0e663dff;
    font-size: 1.25rem;
    font-weight: 700;
    text-shadow: 0 2px 8px rgba(0,0,0,0.18);
    margin-bottom: 0.5rem;
    margin-top: 0;
    letter-spacing: 0.01em;
}

.enlaceTema .ifhover {
    position: absolute;
    right: 1.2rem;
    bottom: 0.7rem;
    font-size: 0.95rem;
    color: #0e663dff;
    opacity: 0.7;
    transition: opacity 0.2s;
    z-index: 2;
}

.enlaceTema:hover {
    filter: brightness(0.85) saturate(1.1);
    transform: translateY(-6px) scale(1.02);
    box-shadow: 0 10px 32px rgba(0,0,0,0.18);
    background: inherit !important;
}

.enlaceTema:active {
    filter: brightness(0.8);
    transform: translateY(-2px) scale(0.99);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.enlaceTema:hover .ifhover {
    opacity: 1;
}

/* Opcional: para que el fondo no cambie de color en hover, solo se oscurezca */
.enlaceTema {
    background-blend-mode: multiply;
}

/* Responsive */
@media (max-width: 768px) {
    .enlaceTema {
        min-height: 100px;
    }
    .enlaceTema i.bi {
        font-size: 2rem;
    }
    .enlaceTema h5 {
        font-size: 1rem;
    }
}
</style>