<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'SIGEM')</title>
    <link rel="icon" type="image/png" href="{{ asset('sige1.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .top-bar {
            background-color: #2a6e48;
            color: white;
            padding: 5px 15px;
            font-size: 14px;
            display: flex;
            justify-content: space-between;
        }
        .navbar-custom {
            background-color: #dce7cd;
            border-bottom: 4px solid #f6ed2f;
            padding: 10px;
            text-align: center;
        }
        .navbar-custom a {
            font-weight: bold;
            color: black;
            margin: 0 15px;
            text-decoration: none;
        }
        .footer-logos {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 40px;
            padding-top: 20px;
            gap: 60px;
            border-top: 1px solid #ccc;
        }
        .footer-logos img {
            height: 80px;
        }
    </style>
</head>
<body>
    <div class="top-bar">
        <div>Instituto Municipal de Investigación y Planeación</div>
        <div>Ciudad Juárez, Chihuahua</div>
    </div>

    <div class="text-center my-3">
        <img src="{{ asset('sige1.png') }}" alt="Logo SIGEM" height="70">
        <img src="{{ asset('sige2.png') }}" alt="Logo SIGEM 2" height="70">
    </div>

    <div class="navbar-custom">
        <a href="{{ url('/') }}">INICIO</a>
        <a href="{{ url('/estadistica.php') }}">ESTADÍSTICA</a>
        <a href="{{ url('/cartografia.php') }}">CARTOGRAFÍA</a>
        <a href="{{ url('/productos.php') }}">PRODUCTOS</a>
        <a href="{{ url('/catalogo.php') }}">CATÁLOGO</a>
    </div>

    <div class="container my-4">
        @yield('content')
    </div>

    <div class="footer-logos">
        <img src="{{ asset('logosfinales2.png') }}" alt="IMIP">
        <img src="{{ asset('logoadmin.png') }}" alt="Ciudad Juárez">
        <img src="{{ asset('sige2.png') }}" alt="SIGEM Footer">
    </div>
</body>
</html>
