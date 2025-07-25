<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sección Geográfico - SIGEM</title>
    <!-- Incluimos Bootstrap desde CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background-color: #f8f9fa;">

    <div class="container mt-5">
        <h1 class="text-center mb-4" style="color: #2a6e48;">Sección Geográfico</h1>

        <div class="row justify-content-center">
            @foreach ($items as $item)
                <div class="col-md-4">
                    <div class="card mb-4 shadow-sm">
                        <div class="card-body text-center">
                            <img src="{{ asset('images/' . $item->icono_path) }}" alt="Icono" class="mb-3" width="50">
                            <h5 class="card-title">{{ $item->nombre_item }}</h5>
                            <a href="{{ url($item->link) }}" class="btn btn-success mt-3">Ver información</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>

    <!-- Script de Bootstrap (opcional si necesitas funcionalidades JS como dropdowns) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
