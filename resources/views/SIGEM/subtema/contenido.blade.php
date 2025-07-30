@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

{{-- Mostrar contenido renderizado --}}
@if($contenido->ce_contenido)
    <div id="bloque-html" class="mb-4">
        {!! $contenido->ce_contenido !!}
    </div>
@endif


    <form method="POST" action="{{ route('contenido.update', $contenido->ce_contenido_id) }}" id="form-edicion">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="titulo">Título:</label>
            <input type="text" name="titulo" id="titulo" class="form-control" value="{{ $titulo }}">
        </div>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Concepto</th>
                    <th>Valor</th>
                </tr>
            </thead>
            <tbody>
                @foreach($datos as $dato)
                    <tr>
                        <td><input type="text" name="conceptos[]" class="form-control" value="{{ $dato['concepto'] }}"></td>
                        <td><input type="text" name="valores[]" class="form-control" value="{{ $dato['valor'] }}"></td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <button type="submit" class="btn btn-warning">Guardar cambios</button>
    </form>

    <script>
        document.getElementById('form-edicion').addEventListener('submit', function(e) {
            e.preventDefault();

            const form = e.target;
            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'text/html',
                    'X-CSRF-TOKEN': formData.get('_token'),
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) throw new Error("Error al guardar");
                return response.text();
            })
            .then(() => {
                setTimeout(() => {
                    if (typeof actualizarContenido === 'function') {
                        actualizarContenido();
                    } else {
                        location.reload();
                    }
                }, 300);
            })
            .catch(error => {
                alert("Error al guardar.");
                console.error(error);
            });
        });
    </script>
