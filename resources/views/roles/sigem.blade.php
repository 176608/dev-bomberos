<!-- Archivo SIGEM -Base de vista sigem publica- - NO ELIMINAR COMENTARIO -->
@extends('layouts.app')

@section('title', 'SIGEM - Sistema de Información Geográfica')

@section('content')
<style>
.header-logos {
    display: flex;
    width: 100%;
    height: 120px;
    background-color: white;
    border-bottom: 4px solid #ffd700;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.header-logos img {
    flex: 1;
    height: 100%;
    object-fit: contain;
    padding: 10px;
}

.header-logos img:first-child {
    border-right: 1px solid #e9ecef;
}
</style>

<div class="container-fluid">

<div class="header-logos container-fluid">
    <img src="../imagenes/sige1.png" alt="IMIP Logo">
    <img src="../imagenes/sige2.png" alt="SIGEM Logo">
</div>

<div class="main-menu container-fluid" style="background-color: #2a6e48; border-bottom: 4px solid #ffd700; display: flex; justify-content: center;">
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