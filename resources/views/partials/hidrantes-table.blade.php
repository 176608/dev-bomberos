<div class="card mt-4 mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title m-0">Reporte Hidrantes Configurado</h5>
        </div>
        <div id="tablaLoader" class="text-center my-5">
            <div class="spinner-border text-primary" role="status"></div>
            <div>Cargando tabla...</div>
        </div>
        <div class="table-responsive" style="display:none;">
            <table id="hidrantesConfigTable" class="table table-striped table-hover table-bordered w-100">
                <thead class="table-dark">
                    <tr>
                        <th class="text-center align-middle">ID</th>
                        <th class="text-center align-middle">stat</th> <!-- NUEVO: stat después de ID -->
                        @foreach($columnas as $columna)
                            @if($columna !== 'id' && $columna !== 'acciones' && $columna !== 'stat')
                                <th class="text-center align-middle">{{ $headerNames[$columna] ?? ucfirst($columna) }}</th>
                            @endif
                        @endforeach
                        <th class="text-center align-middle">Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
<script>
window.hidrantesTableConfig = @json($columnas);
window.hidrantesHeaderNames = @json($headerNames);
</script>