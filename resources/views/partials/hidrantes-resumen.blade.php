<!-- Archivo Bomberos - NO ELIMINAR COMENTARIO -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <div class="row align-items-center">
                    <div class="col-12">
                        <h5 class="mb-0">Resumen de Hidrantes por Estación y {{ $titulo_resumen }}</h5>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tablaResumenHidrantes" class="table table-bordered table-striped text-center align-middle">
                        <thead class="table-primary">
                            <tr>
                                <th>ESTACION</th>
                                @foreach($columnas as $titulo => $config)
                                    <th class="{{ $config['clase'] }}">{{ $titulo }}</th>
                                @endforeach
                                <th class="bg-secondary text-white">TOTAL X ESTACION</th>
                                <th class="{{ $ultima_columna['clase'] }}">{{ $ultima_columna['titulo'] }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($estaciones as $est => $row)
                                <tr>
                                    <td>{{ $est }}</td>
                                    @foreach($columnas as $titulo => $config)
                                        <td>{{ $row[$config['key']] }}</td>
                                    @endforeach
                                    <td>{{ $row['TOTAL'] }}</td>
                                    <td>{{ $row[$ultima_columna['key']] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="no-sort">
                                <td><b>TOTALES</b></td>
                                @foreach($columnas as $titulo => $config)
                                    <td><b>{{ $totales[$config['key']] }}</b></td>
                                @endforeach
                                <td><b>{{ $totales['TOTAL'] }}</b></td>
                                <td><b>{{ array_sum(array_map(function($row) use($ultima_columna) {
                                    return $row[$ultima_columna['key']];
                                }, $estaciones)) }}</b></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
</script>