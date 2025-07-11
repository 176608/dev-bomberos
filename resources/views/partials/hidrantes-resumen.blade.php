<div class="card mt-4">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0">Resumen de Hidrantes por Estación y Estado</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="tablaResumenHidrantes" class="table table-bordered table-striped text-center align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>ESTACION</th>
                        <th class="bg-info text-white">FUNCIONANDO</th>
                        <th class="bg-danger text-white">FUERA DE SERVICIO</th>
                        <th class="bg-warning text-dark">SOLO BASE</th>
                        <th class="bg-secondary text-white">TOTAL X ESTACION</th>
                        <th class="bg-warning text-dark">TOTAL F.S. + S.B.</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($estaciones as $est => $row)
                        <tr>
                            <td>{{ $est }}</td>
                            <td>{{ $row['EN SERVICIO'] }}</td>
                            <td>{{ $row['FUERA DE SERVICIO'] }}</td>
                            <td>{{ $row['SOLO BASE'] }}</td>
                            <td>{{ $row['TOTAL'] }}</td>
                            <td>{{ $row['FS_SB'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="no-sort">
                        <td><b>TOTALES</b></td>
                        <td><b>{{ $totales['EN SERVICIO'] }}</b></td>
                        <td><b>{{ $totales['FUERA DE SERVICIO'] }}</b></td>
                        <td><b>{{ $totales['SOLO BASE'] }}</b></td>
                        <td><b>{{ $totales['TOTAL'] }}</b></td>
                        <td><b>{{ $totales['FUERA DE SERVICIO'] + $totales['SOLO BASE'] }}</b></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="alert alert-info">
                    <strong>Porcentaje:</strong><br>
                    <span class="text-success">{{ $porcentajes['EN SERVICIO'] }}% EN SERVICIO</span><br>
                    <span class="text-danger">{{ $porcentajes['FUERA DE SERVICIO'] }}% FUERA DE SERVICIO</span><br>
                    <span class="text-warning">{{ $porcentajes['SOLO BASE'] }}% SOLO BASE</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- DataTables CSS/JS (si no lo tienes ya en tu layout) -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css"/>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    // Inicializa DataTable solo sobre el tbody (no afecta tfoot)
    $('#tablaResumenHidrantes').DataTable({
        paging: false,
        searching: false,
        info: false,
        orderCellsTop: true,
        order: [],
        columnDefs: [
            { orderable: false, targets: [] } // Todas las columnas ordenables
        ],
        
    });
});
</script>