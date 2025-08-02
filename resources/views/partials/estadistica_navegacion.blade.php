<div class="card shadow-sm">
    <div class="card-body p-0">
        <!-- Cabecera -->
        <div class="row g-0 border-bottom">
            <div class="col-8">
                <div class="p-4">
                    <h2 class="text-success mb-2">
                        <i class="bi bi-bar-chart me-2"></i>Sección Estadística
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
                                    <div class="card h-100 tema-card" data-tema-id="{{ $tema->tema_id }}">
                                        <div class="card-header text-center" style="{{ $colorTema }}">
                                            <h5 class="mb-0 fw-bold">
                                                {{ $tema->orden_indice }}. {{ $tema->tema_titulo }}
                                            </h5>
                                        </div>
                                        <div class="card-body text-center p-4">
                                            <i class="bi bi-folder-fill text-muted" style="font-size: 3rem;"></i>
                                            <p class="mt-3 mb-0">
                                                <small class="text-muted">
                                                    {{ $tema->subtemas ? $tema->subtemas->count() : 0 }} subtemas disponibles
                                                </small>
                                            </p>
                                        </div>
                                        <div class="card-footer text-center">
                                            <a href="{{ route('sigem.estadistica.tema', ['tema_id' => $tema->tema_id]) }}" class="btn btn-outline-primary btn-sm">
                                                <i class="bi bi-arrow-right me-1"></i>Explorar tema
                                            </a>
                                        </div>
                                    </div>
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
}

.tema-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    border-color: #0d6efd;
}

.tema-card:hover .card-footer .btn {
    background-color: #0d6efd;
    color: white;
}
</style>