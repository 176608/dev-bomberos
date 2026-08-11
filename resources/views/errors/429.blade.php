<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Demasiadas solicitudes</title>
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f4f6f8;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            color: #212529;
        }
        .card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            padding: 48px 40px;
            max-width: 480px;
            width: calc(100% - 32px);
            margin: 16px;
            text-align: center;
        }
        .status {
            font-size: 56px;
            font-weight: 700;
            color: #e11d48;
        }
        h1 {
            font-size: 22px;
            margin: 16px 0 8px;
        }
        p {
            font-size: 15px;
            line-height: 1.6;
            color: #4b5563;
            margin: 0 0 24px;
        }
        a.btn {
            display: inline-block;
            background: #2563eb;
            color: #ffffff;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            transition: background 0.15s;
        }
        a.btn:hover {
            background: #1d4ed8;
        }
        .hint {
            margin-top: 16px;
            font-size: 13px;
            color: #9ca3af;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="status">429</div>
        <h1>Demasiadas solicitudes</h1>
        <p>Se han enviado demasiadas solicitudes desde esta dirección en poco tiempo.
           Espera unos minutos e inténtalo de nuevo.</p>
        <a class="btn" href="{{ route('login') }}">Ir al inicio de sesión</a>
        <div class="hint">Si el problema continúa, contacta al administrador del sistema.</div>
    </div>
</body>
</html>