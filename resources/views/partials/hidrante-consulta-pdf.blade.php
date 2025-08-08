<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Reporte de Hidrante #{{ $hidrante->id }}</title>
    <style>
        /* Estilos base */
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            line-height: 1.5;
            color: #333;
            margin: 0;
            padding: 0;
        }
        
        /* Contenedores */
        .container-fluid {
            width: 100%;
            padding: 0;
        }
        
        .card {
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
            margin-bottom: 1rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            background-color: #fff;
        }
        
        .card-header {
            padding: 0.75rem 1.25rem;
            border-bottom: 1px solid #dee2e6;
            font-weight: bold;
        }
        
        .card-body {
            padding: 1.25rem;
        }
        
        .card-footer {
            padding: 0.75rem 1.25rem;
            border-top: 1px solid #dee2e6;
            color: #6c757d;
            font-size: 0.875rem;
        }
        
        /* Colores de fondo */
        .bg-danger {
            background-color: #dc3545;
            color: white;
        }
        
        /* Badges y estados */
        .badge {
            display: inline-block;
            padding: 0.25em 0.4em;
            font-size: 75%;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.25rem;
        }
        
        .bg-success {
            background-color: #28a745;
            color: white;
        }
        
        .bg-warning {
            background-color: #ffc107;
            color: #212529;
        }
        
        .bg-secondary {
            background-color: #6c757d;
            color: white;
        }
        
        /* Layout */
        .row {
            display: block;
            clear: both;
            margin-bottom: 10px;
        }
        
        .row:after {
            content: "";
            display: table;
            clear: both;
        }
        
        .col-md-6, .col-md-12 {
            float: left;
            padding: 0 15px;
        }
        
        .col-md-6 {
            width: 50%;
        }
        
        .col-md-12 {
            width: 100%;
        }
        
        /* Alineación y texto */
        .text-center {
            text-align: center;
        }
        
        .fw-bold {
            font-weight: bold;
        }
        
        .mb-0 {
            margin-bottom: 0;
        }
        
        .ms-1 {
            margin-left: 4px;
        }
        
        .mb-2 {
            margin-bottom: 0.5rem;
        }
        
        hr {
            border: 0;
            border-top: 1px solid #dee2e6;
            margin: 1rem 0;
        }
        
        /* Título y encabezado */
        .header-banner {
            background-color: #dc3545;
            color: white;
            padding: 10px;
            margin-bottom: 15px;
            text-align: center;
        }
        
        .header-title {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }
        
        .header-subtitle {
            font-size: 14px;
            margin: 0;
        }
        
        /* Otros estilos */
        .location-box {
            border: 1px solid #dee2e6;
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 0.25rem;
            margin: 15px 0;
        }
        
        .location-title {
            background-color: #e9ecef;
            padding: 8px;
            margin: -10px -10px 10px -10px;
            border-bottom: 1px solid #dee2e6;
            text-align: center;
            font-weight: bold;
        }
        
        .info-row {
            margin-bottom: 10px;
        }
        
        .info-label {
            font-weight: bold;
            display: inline-block;
            margin-right: 5px;
        }
        
        img.escudo {
            height: 90px;
            margin: 10px auto;
            display: block;
        }
        
        .update-info {
            font-size: 12px;
            color: #6c757d;
            text-align: right;
            border-top: 1px solid #dee2e6;
            padding-top: 5px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <!-- Encabezado principal -->
        <div class="header-banner">
            <h1 class="header-title">Departamento de Bomberos</h1>
            <p class="header-subtitle">Registro de Hidrantes</p>
        </div>
        
        <!-- Información principal -->
        <div class="row">
            <div class="col-md-6">
                <img src="{{ public_path('img/logo/Escudo_Ciudad_Juarez_smn.png') }}" alt="Escudo Ciudad Juárez" class="escudo">
            </div>
            <div class="col-md-6">
                <div class="info-row">
                    <span class="info-label">Fecha de Inspección:</span>
                    {{ $hidrante->fecha_inspeccion ? \Carbon\Carbon::parse($hidrante->fecha_inspeccion)->format('d/m/Y') : 'No registrada' }}
                </div>
                <div class="info-row">
                    <span class="info-label">N° Estación:</span>
                    {{ $hidrante->numero_estacion ?: 'No registrada' }}
                </div>
                <div class="info-row">
                    <span class="info-label">N° Hidrante:</span>
                    {{ $hidrante->id }}
                </div>
            </div>
        </div>
        
        <hr>
        
        <!-- Ubicación del Hidrante -->
        <div class="location-box">
            <div class="location-title">Ubicación del Hidrante</div>
            
            <div class="text-center mb-2">
                @php
                    $callePrincipal = $hidrante->callePrincipal 
                        ? $hidrante->callePrincipal->Tipovial . ' ' . $hidrante->callePrincipal->Nomvial
                        : ($hidrante->calle ? $hidrante->calle . '*' : null);
                        
                    $calleSecundaria = $hidrante->calleSecundaria 
                        ? $hidrante->calleSecundaria->Tipovial . ' ' . $hidrante->calleSecundaria->Nomvial
                        : ($hidrante->y_calle ? $hidrante->y_calle . '*' : null);
                @endphp

                @if($callePrincipal)
                    @if($calleSecundaria)
                        Entre {{ $callePrincipal }} y {{ $calleSecundaria }}.
                    @else
                        Sobre {{ $callePrincipal }}.
                    @endif
                @else
                    Ubicación no registrada.
                @endif
            </div>
            
            <div class="text-center mb-2">
                @if($hidrante->coloniaLocacion)
                    En {{ $hidrante->coloniaLocacion->TIPO }} {{ $hidrante->coloniaLocacion->NOMBRE }}.
                @elseif($hidrante->colonia && $hidrante->colonia !== NULL)
                    En {{ $hidrante->colonia }}*.
                @else
                    Colonia no registrada.
                @endif
            </div>
            
            <div class="update-info">
                Última actualización realizada: {{ $hidrante->updated_at ? \Carbon\Carbon::parse($hidrante->updated_at)->format('d/m/Y H:i') : 'N/A' }}
            </div>
        </div>
        
        <hr>
        
        <!-- Estado y presión -->
        <div class="row">
            <div class="col-md-6">
                <span class="info-label">Estado de Hidrante:</span>
                @if($hidrante->estado_hidrante === 'S/I')
                    S/I
                @elseif($hidrante->estado_hidrante == 'BUENO')
                    <span class="badge bg-success">BUENO</span>
                @elseif($hidrante->estado_hidrante == 'REGULAR')
                    <span class="badge bg-warning">REGULAR</span>
                @elseif($hidrante->estado_hidrante == 'MALO')
                    <span class="badge bg-danger">MALO</span>
                @elseif($hidrante->estado_hidrante == 'NO FUNCIONA')
                    <span class="badge bg-secondary">NO FUNCIONA</span>
                @else
                    {{ ucfirst(strtolower($hidrante->estado_hidrante)) ?: 'No registrado' }}
                @endif
            </div>
            <div class="col-md-6">
                <span class="info-label">Presión del Agua:</span>
                @if($hidrante->presion_agua === 'S/I')
                    S/I
                @else
                    {{ ucfirst(strtolower($hidrante->presion_agua)) ?: 'No registrada' }}
                @endif
            </div>
        </div>
        
        <hr>
        
        <!-- Marca y año -->
        <div class="row">
            <div class="col-md-6">
                <span class="info-label">Marca:</span>
                {{ $hidrante->marca ?: 'No registrada' }}
            </div>
            <div class="col-md-6">
                <span class="info-label">Año:</span>
                {{ $hidrante->anio ?: 'No registrado' }}
            </div>
        </div>
        
        <hr>
        
        <!-- Llaves -->
        <div class="row">
            <div class="col-md-6">
                <span class="info-label">Llave de Hidrante:</span>
                @if($hidrante->llave_hidrante === 'S/I')
                    S/I
                @else
                    {{ ucfirst(strtolower($hidrante->llave_hidrante)) ?: 'No registrada' }}
                @endif
            </div>
            <div class="col-md-6">
                <span class="info-label">Llave de Fosa:</span>
                @if($hidrante->llave_fosa === 'S/I')
                    S/I
                @else
                    {{ ucfirst(strtolower($hidrante->llave_fosa)) ?: 'No registrada' }}
                @endif
            </div>
        </div>
        
        <hr>
        
        <!-- Ubicación de fosa y tubo -->
        <div class="row">
            <div class="col-md-6">
                <span class="info-label">Ubicación de Fosa:</span>
                @if($hidrante->ubicacion_fosa === 'S/I')
                    S/I
                @elseif(is_numeric($hidrante->ubicacion_fosa) && $hidrante->ubicacion_fosa >= 1 && $hidrante->ubicacion_fosa <= 100)
                    A {{ $hidrante->ubicacion_fosa }} Metros
                @else
                    {{ ucfirst(strtolower($hidrante->ubicacion_fosa)) ?: 'No registrada' }}
                @endif
            </div>
            <div class="col-md-6">
                <span class="info-label">Hidrante Conectado a Tubo de:</span>
                @switch($hidrante->hidrante_conectado_tubo)
                    @case("S/I")
                        S/I
                        @break
                    @case("4'")
                        4 Pulgadas
                        @break
                    @case("6'")
                        6 Pulgadas
                        @break
                    @case("8'")
                        8 Pulgadas
                        @break
                    @default
                        {{ ucfirst(strtolower($hidrante->hidrante_conectado_tubo)) ?: 'No registrado' }}
                @endswitch
            </div>
        </div>
        
        <hr>
        
        <!-- Observaciones y oficial -->
        <div class="row">
            <div class="col-md-12">
                <span class="info-label">Observaciones:</span>
                {{ $hidrante->observaciones ?: 'No hay observaciones registradas' }}
            </div> 
        </div>
        <div class="row">
            <div class="col-md-12">
                <span class="info-label">Oficial:</span>
                {{ $hidrante->oficial ?: 'No registrado' }}
            </div>
        </div>
    </div>
</body>
</html>