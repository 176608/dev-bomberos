<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 — Página no encontrada</title>
    <style>
        body { font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif; background: #f6f7f9; color: #212529; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        .card { background: #fff; border: 1px solid #dee2e6; border-radius: 8px; padding: 2.5rem; max-width: 540px; text-align: center; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06); margin: 1rem; }
        .codigo { font-size: 3.2rem; font-weight: 700; color: #adb5bd; margin: 0; line-height: 1; }
        h1 { font-size: 1.25rem; margin: 0.5rem 0 1rem; }
        p { color: #6c757d; font-size: 0.95rem; line-height: 1.5; margin: 0; }
        .acciones { margin-top: 1.5rem; display: flex; gap: 0.75rem; justify-content: center; flex-wrap: wrap; }
        .enlace { text-decoration: none; font-size: 0.9rem; padding: 0.5rem 1rem; border-radius: 6px; border: 1px solid transparent; }
        .enlace-primario { background: #2c5f4a; color: #fff; }
        .enlace-primario:hover { background: #234c3b; color: #fff; }
        .enlace-secundario { background: #fff; color: #2c5f4a; border-color: #2c5f4a; }
        .enlace-secundario:hover { background: #eef4f1; }
    </style>
</head>
<body>
    <div class="card">
        <p class="codigo">404</p>
        <h1>Página no encontrada</h1>
        <p>El recurso solicitado no existe o no está disponible. Puede que la dirección haya cambiado o que el contenido no esté publicado.</p>
        <div class="acciones">
            <a class="enlace enlace-primario" href="/">Inicio</a>
            <a class="enlace enlace-secundario" href="/sigem">SIGEM</a>
            <a class="enlace enlace-secundario" href="/biblioteca">Biblioteca</a>
            <a class="enlace enlace-secundario" href="#" onclick="history.back(); return false;">Regresar</a>
        </div>
    </div>
</body>
</html>
