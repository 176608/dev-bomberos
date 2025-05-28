@extends('layouts.app')

@section('title', 'Bomberos')

@section('content')

<style>
    .custom-image-size {
        width: 40vw;
        height: auto;
        object-fit: contain;
    }

    .card-title {
        margin-left: 1rem;
        font-weight: bold;
    }
</style>

<div class="container mt-4">

<div class="card mb-4">
    <div class="card-body">

        <!-- Uso de row y col-6 -->
        <div class="row g-3 align-items-center">

            <!-- Columna izquierda (6/12) - Imagen -->
            <div class="col-6 d-flex align-items-center">
                <img src="{{ asset('img/logo/Escudo_Ciudad_Juarez.png') }}" alt="Escudo" class="img-fluid custom-image-size">
            </div>

            <!-- Columna derecha (6/12) - Botones -->
            <div class="col-6 d-flex flex-column justify-content-center">
                <button class="btn btn-primary mb-2">Alta de hidrante</button>
                <button class="btn btn-secondary mb-2">Editar parametros del reporte</button>
                <button class="btn btn-success" id="verReporteBtn" data-bs-toggle="collapse" data-bs-target="#tabla-hidrantes">
                    Ver reporte de hidrantes <i class="bi bi-chevron-down"></i>
                </button>
            </div>

        </div>
    </div>
</div>

    <!-- Accordion con tabla -->
    <div class="accordion" id="accordionHidrantes">
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#tabla-hidrantes" aria-expanded="false">
                    Mostrar/Ocultar Reporte de Hidrantes
                </button>
            </h2>
            <div id="tabla-hidrantes" class="accordion-collapse collapse">
                <div class="accordion-body">
                    <div class="table-responsive">
                        <table id="hidrantesTable" class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Fecha Alta</th>
                                    <th>Calle</th>
                                    <th>Y Calle</th>
                                    <th>Colonia</th>
                                    <th>Marca</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($hidrantes as $hidrante)
                                    <tr>
                                        <td>{{ $hidrante->id }}</td>
                                        <td>{{ $hidrante->fecha_alta }}</td>
                                        <td>{{ $hidrante->callePrincipal ? $hidrante->callePrincipal->Nomvial : 'Calle Principal no especificada' }}</td>
                                        <td>{{ $hidrante->calleSecundaria ? $hidrante->calleSecundaria->Nomvial : 'Calle Secundaria no especificada' }}</td>
                                        <td>{{ $hidrante->coloniaLocacion ? $hidrante->coloniaLocacion->NOMBRE : 'Colonia no especificada' }}</td>
                                        <td>{{ $hidrante->marca ? $hidrante->marca : 'S/A' }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editarHidranteModal{{ $hidrante->id }}">
                                                Editar
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Modal de Edición -->
<div class="modal fade" id="editarHidranteModal{{ $hidrante->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('hidrantes.update', $hidrante->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Editar Hidrante #{{ $hidrante->id }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Número de Estación</label>
                            <input type="number" class="form-control" name="numero_estacion" value="{{ $hidrante->numero_estacion }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Número de Hidrante</label>
                            <input type="number" class="form-control" name="numero_hidrante" value="{{ $hidrante->numero_hidrante }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Calle Principal</label>
                            <select class="form-select" name="id_calle" required>
                                @foreach($calles as $calle)
                                    <option value="{{ $calle->IDKEY }}" {{ $hidrante->id_calle == $calle->IDKEY ? 'selected' : '' }}>
                                        {{ $calle->Nomvial }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Calle Secundaria</label>
                            <select class="form-select" name="id_y_calle" required>
                                @foreach($calles as $calle)
                                    <option value="{{ $calle->IDKEY }}" {{ $hidrante->id_y_calle == $calle->IDKEY ? 'selected' : '' }}>
                                        {{ $calle->Nomvial }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Colonia</label>
                            <select class="form-select" name="id_colonia" required>
                                @foreach($colonias as $colonia)
                                    <option value="{{ $colonia->IDKEY }}" {{ $hidrante->id_colonia == $colonia->IDKEY ? 'selected' : '' }}>
                                        {{ $colonia->NOMBRE }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Llave Hidrante</label>
                            <select class="form-select" name="llave_hidrante">
                                <option value="Si" {{ $hidrante->llave_hidrante == 'Si' ? 'selected' : '' }}>Si</option>
                                <option value="No" {{ $hidrante->llave_hidrante == 'No' ? 'selected' : '' }}>No</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Presión de Agua</label>
                            <input type="text" class="form-control" name="presion_agua" value="{{ $hidrante->presion_agua }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Color</label>
                            <input type="text" class="form-control" name="color" value="{{ $hidrante->color }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Llave Fosa</label>
                            <select class="form-select" name="llave_fosa">
                                <option value="Si" {{ $hidrante->llave_fosa == 'Si' ? 'selected' : '' }}>Si</option>
                                <option value="No" {{ $hidrante->llave_fosa == 'No' ? 'selected' : '' }}>No</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Ubicación Fosa</label>
                            <input type="text" class="form-control" name="ubicacion_fosa" value="{{ $hidrante->ubicacion_fosa }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Conectado a Tubo</label>
                            <select class="form-select" name="hidrante_conectado_tubo">
                                <option value="Si" {{ $hidrante->hidrante_conectado_tubo == 'Si' ? 'selected' : '' }}>Si</option>
                                <option value="No" {{ $hidrante->hidrante_conectado_tubo == 'No' ? 'selected' : '' }}>No</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Estado Hidrante</label>
                            <select class="form-select" name="estado_hidrante">
                                <option value="Bueno" {{ $hidrante->estado_hidrante == 'Bueno' ? 'selected' : '' }}>Bueno</option>
                                <option value="Regular" {{ $hidrante->estado_hidrante == 'Regular' ? 'selected' : '' }}>Regular</option>
                                <option value="Malo" {{ $hidrante->estado_hidrante == 'Malo' ? 'selected' : '' }}>Malo</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Marca</label>
                            <input type="text" class="form-control" name="marca" value="{{ $hidrante->marca }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Año</label>
                            <input type="number" class="form-control" name="anio" value="{{ $hidrante->anio }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Observaciones</label>
                        <textarea class="form-control" name="observaciones" rows="3">{{ $hidrante->observaciones }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Oficial</label>
                        <select class="form-select" name="oficial">
                            <option value="Si" {{ $hidrante->oficial == 'Si' ? 'selected' : '' }}>Si</option>
                            <option value="No" {{ $hidrante->oficial == 'No' ? 'selected' : '' }}>No</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Inicializar DataTable
        var table = $('#hidrantesTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
            },
            order: [[0, 'desc']],
            paging: true,
            searching: true,
            info: true,
            autoWidth: false
        });

        // Funcionalidad del botón "Ver reporte"
        $('#verReporteBtn').on('click', function () {
            const target = document.getElementById('tabla-hidrantes');
            if (target.classList.contains('show')) {
                $('html, body').animate({ scrollTop: $('#accordionHidrantes').offset().top }, 1000);
            } else {
                setTimeout(() => {
                    $('html, body').animate({ scrollTop: $('#accordionHidrantes').offset().top }, 1000);
                }, 300); // Pequeño retraso para esperar que el accordion se abra
            }
        });
    });
</script>
@endsection