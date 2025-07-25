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

@php
    session_start();
    $esAdmin = isset($_SESSION['usuario']) && $_SESSION['usuario'] === 'admin';
@endphp

@if($esAdmin)
    @php
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $html = mb_convert_encoding($contenido->ce_contenido, 'HTML-ENTITIES', 'UTF-8');
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);
        $rows = $dom->getElementsByTagName('tr');

        $titulo = '';
        $datos = [];

        foreach ($rows as $i => $tr) {
            $tds = $tr->getElementsByTagName('td');
            if ($i === 0 && $tds->length === 1 || $tds->length === 2 && $tds[0]->getAttribute('colspan') === '2') {
                $titulo = trim($tds[0]->nodeValue);
            } elseif ($tds->length === 2) {
                $datos[] = [
                    'concepto' => trim($tds[0]->nodeValue),
                    'valor' => trim($tds[1]->nodeValue),
                ];
            }
        }
    @endphp

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
@endif
