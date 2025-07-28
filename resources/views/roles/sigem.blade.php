<!-- Archivo SIGEM - NO ELIMINAR COMENTARIO -->
@extends('layouts.app')

@section('title', 'SIGEM - Sistema de Información Geográfica')

@section('content')
<style>
.header-logos {
            display: flex;
            align-items: center;
            gap: 20px;
            margin: 10px 0 10px 10px;
        }
        .header-logos img:first-child { height: 85px; }
        .header-logos img:last-child { height: 65px; }
</style>

<div class="container-fluid">

<div class="header-logos">
    <img src="imagenes/sige1.png" alt="IMIP Logo">
    <img src="imagenes/sige2.png" alt="SIGEM Logo">
</div>

<div class="main-menu" style="background-color: #2a6e48; border-bottom: 4px solid #ffd700; display: flex; justify-content: center;">
    <a href="#" style="color: white; text-decoration: none; padding: 10px 18px; font-weight: bold;">INICIO</a>
    <a href="#" style="color: white; text-decoration: none; padding: 10px 18px; font-weight: bold;">ESTADÍSTICA</a>
    <a href="#" style="color: white; text-decoration: none; padding: 10px 18px; font-weight: bold;">CARTOGRAFÍA</a>
    <a href="#" style="color: white; text-decoration: none; padding: 10px 18px; font-weight: bold;">PRODUCTOS</a>
    <a href="#" style="color: white; text-decoration: none; padding: 10px 18px; font-weight: bold;">CATÁLOGO</a>
</div>
    <div id="sigem-content" class="mt-4">
        <!-- Aquí se cargarán los partial de los distintos temas -->
    </div>
</div>
@endsection

@section('scripts')
<script>

</script>
@endsection