<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Reporte de Hidrante #{{ $hidrante->id }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            line-height: 1.5;
            color: #333;
            margin: 0;
            padding: 0;
        }
        
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
        
        .bg-danger {
            background-color: #dc3545;
            color: white;
        }
        
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
            box-sizing: border-box;
        }
        
        .col-md-6 {
            width: 45%;
        }
        
        .col-md-12 {
            width: 95%;
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
            width: 100%;
            margin-bottom: 2px;
        }
        
        .info-value {
            font-weight: normal;
            display: block;
            padding-left: 10px;
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
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .data-table td {
            padding: 8px 5px;
            vertical-align: top;
        }
        
        .data-table .label-cell {
            font-weight: bold;
            width: 40%;
        }
        
        .data-table .value-cell {
            font-weight: normal;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="header-banner">
            <h1 class="header-title">Departamento de Bomberos</h1>
            <p class="header-subtitle">Registro de Hidrantes</p>
        </div>
        
        <div class="row">
            <div class="col-md-6" style="text-align: center;">
                <img src="{{ public_path('img/logo/Escudo_Ciudad_Juarez_smn.png') }}" alt="Escudo Ciudad Juárez" class="escudo">
            </div>
            <div class="col-md-6">
                <table class="data-table">
                    <tr>
                        <td class="label-cell">Fecha de Inspección:</td>
                        <td class="value-cell">{{ $hidrante->fecha_inspeccion ? \Carbon\Carbon::parse($hidrante->fecha_inspeccion)->format('d/m/Y') : 'No registrada' }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">N° Estación:</td>
                        <td class="value-cell">{{ $hidrante->numero_estacion ?: 'No registrada' }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">N° Hidrante:</td>
                        <td class="value-cell">{{ $hidrante->id }}</td>
                    </tr>
                </table>
            </div>
        </div>
        
        <hr>
        
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
        
        <table class="data-table">
            <tr>
                <td class="label-cell">Estado de Hidrante:</td>
                <td class="value-cell">
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
                </td>
                <td class="label-cell">Presión del Agua:</td>
                <td class="value-cell">
                    @if($hidrante->presion_agua === 'S/I')
                        S/I
                    @else
                        {{ ucfirst(strtolower($hidrante->presion_agua)) ?: 'No registrada' }}
                    @endif
                </td>
            </tr>
            
            <tr>
                <td class="label-cell">Marca:</td>
                <td class="value-cell">{{ $hidrante->marca ?: 'No registrada' }}</td>
                <td class="label-cell">Año:</td>
                <td class="value-cell">{{ $hidrante->anio ?: 'No registrado' }}</td>
            </tr>
            
            <tr>
                <td class="label-cell">Llave de Hidrante:</td>
                <td class="value-cell">
                    @if($hidrante->llave_hidrante === 'S/I')
                        S/I
                    @else
                        {{ ucfirst(strtolower($hidrante->llave_hidrante)) ?: 'No registrada' }}
                    @endif
                </td>
                <td class="label-cell">Llave de Fosa:</td>
                <td class="value-cell">
                    @if($hidrante->llave_fosa === 'S/I')
                        S/I
                    @else
                        {{ ucfirst(strtolower($hidrante->llave_fosa)) ?: 'No registrada' }}
                    @endif
                </td>
            </tr>
            
            <tr>
                <td class="label-cell">Ubicación de Fosa:</td>
                <td class="value-cell">
                    @if($hidrante->ubicacion_fosa === 'S/I')
                        S/I
                    @elseif(is_numeric($hidrante->ubicacion_fosa) && $hidrante->ubicacion_fosa >= 1 && $hidrante->ubicacion_fosa <= 100)
                        A {{ $hidrante->ubicacion_fosa }} Metros
                    @else
                        {{ ucfirst(strtolower($hidrante->ubicacion_fosa)) ?: 'No registrada' }}
                    @endif
                </td>
                <td class="label-cell">Hidrante Conectado a Tubo de:</td>
                <td class="value-cell">
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
                </td>
            </tr>
            
            <tr>
                <td class="label-cell">Observaciones:</td>
                <td class="value-cell" colspan="3">{{ $hidrante->observaciones ?: 'No hay observaciones registradas' }}</td>
            </tr>
            
            <tr>
                <td class="label-cell">Oficial:</td>
                <td class="value-cell" colspan="3">{{ $hidrante->oficial ?: 'No registrado' }}</td>
            </tr>
        </table>
    </div>
</body>
</html>