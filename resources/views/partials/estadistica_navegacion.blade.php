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
                                <div class="col-lg-4 col-md-6 mb-4">
                                    <a href="{{ route('sigem.estadistica.tema', ['tema_id' => $tema->tema_id]) }}" 
                                       class="card h-100 tema-card text-decoration-none" 
                                       data-tema-id="{{ $tema->tema_id }}">
                                        <div class="card-header text-center" style="{{ $colorTema }}">
                                            <h5 class="mb-0 fw-bold">
                                                {{ $tema->orden_indice }}. {{ $tema->tema_titulo }}
                                            </h5>
                                        </div>
                                        <div class="card-body text-center p-4">
                                            <p class="mt-3 mb-0">
                                                <small class="text-muted">
                                                    {{ $tema->subtemas ? $tema->subtemas->count() : 0 }} subtemas disponibles
                                                </small>
                                            </p>
                                        </div>
                                        <div class="card-footer text-center">
                                            <span class="text-success">
                                                <i class="bi bi-hand-index-thumb me-1"></i>Da click para explorar
                                            </span>
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
.tema-card {
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid transparent;
    display: block; /* Asegura que el enlace ocupe toda la tarjeta */
    color: inherit; /* Mantiene colores de texto originales */
}

.tema-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    border-color: #8d247fff;
    text-decoration: none !important;
}

.tema-card .card-footer {
    transition: background-color 0.3s ease;
}

.tema-card:hover .card-footer {
    background-color: #2f5e8dff;
}

.tema-card .card-footer span {
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.tema-card:hover .card-footer span {
    color: #1d8d13ff !important;
    font-weight: bold;
}

/* Efecto de pulsación al hacer clic */
.tema-card:active {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

/* Asegura que no se vea como un enlace tradicional */
.tema-card.text-decoration-none:hover {
    text-decoration: none !important;
}
</style>