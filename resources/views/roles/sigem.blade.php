<!-- Archivo SIGEM -Base de vista sigem publica- - NO ELIMINAR COMENTARIO -->
@extends('layouts.app')

@section('title', 'SIGEM - Sistema de Información Geográfica')

@section('content')
<style>
.header-logos {
    display: flex;
    width: 100%;
    min-height: 100px;
    background: linear-gradient(135deg, #2a6e48 0%, #66d193 50%, #2a6e48 100%);
    border-bottom: 4px solid #ffd700;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.logo-section {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: white;
    margin: 10px 5px;
    border-radius: 8px;
    padding: 15px;
    transition: all 0.3s ease;
}

.logo-section:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(0,0,0,0.15);
}

.logo-section img {
    max-width: 100%;
    max-height: 80px;
    object-fit: contain;
}

/* Responsive */
@media (max-width: 768px) {
    .header-logos {
        flex-direction: column;
        min-height: auto;
    }
    
    .logo-section {
        margin: 5px 10px;
    }
}
</style>

<div class="container-fluid">

<div class="header-logos container-fluid">
    <div class="logo-section">
        <img src="../imagenes/sige1.png" alt="IMIP Logo">
    </div>
    <div class="logo-section">
        <img src="../imagenes/sige2.png" alt="SIGEM Logo">
    </div>
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