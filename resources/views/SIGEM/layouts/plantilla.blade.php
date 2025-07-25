<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'SIGEM')</title>
    <link rel="icon" type="image/png" href="{{ asset('imiplogo_200x200_trans.png') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #eeeeee;
        }
        .top-bar {
            background-color: #2a6e48;
            color: white;
            padding: 5px 15px;
            font-size: 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header-logos {
            display: flex;
            align-items: center;
            gap: 20px;
            margin: 10px 0 10px 10px;
        }
        .header-logos img:first-child { height: 85px; }
        .header-logos img:last-child { height: 65px; }
        .main-nav {
            background-color: #dce7cd;
            border-bottom: 4px solid #f6ed2f;
            padding: 10px 0;
            text-align: center;
        }
        .main-nav a {
            font-weight: bold;
            color: black;
            text-decoration: none;
            margin: 0 15px;
        }
        .main-content {
            background-color: white;
            border-radius: 8px;
            padding: 25px;
            margin: 20px auto;
            width: 90%;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
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
        .footer-logos img { height: 70px; }
    </style>
</head>
<body>

    <div class="top-bar">
        <div>Instituto Municipal de Investigación y Planeación</div>
        <div>Ciudad Juárez, Chihuahua</div>
    </div>

    <div class="header-logos">
        <img src="{{ asset('sige1.png') }}" alt="IMIP Logo">
        <img src="{{ asset('sige2.png') }}" alt="SIGEM Logo">
    </div>

    <div class="main-nav">
        <a href="{{ url('/') }}">INICIO</a>
        <a href="{{ url('estadistica') }}">ESTADÍSTICA</a>
        <a href="{{ url('cartografia') }}">CARTOGRAFÍA</a>
        <a href="{{ url('productos') }}">PRODUCTOS</a>
        <a href="{{ url('catalogo') }}">CATÁLOGO</a>
    </div>

    <div class="main-content">
        @yield('content')
    </div>

    <div class="footer-logos">
        <img src="{{ asset('logosfinales2.png') }}" alt="IMIP Logo">
        <img src="{{ asset('logosfinales1.png') }}" alt="Gobierno Ciudad Juárez">
        <img src="{{ asset('logoadmin.png') }}" alt="Gobierno Municipal">
    </div>
</body>
</html>
