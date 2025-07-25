<?php
session_start();

include '../models/generarGraficaModel.php';

$usuario = $_SESSION['usuario'] ?? 'Público';
$cuadro_id = $_GET['cuadro_id'] ?? ($_POST['cuadro_id'] ?? '');
$tema = $_GET['tema'] ?? 'Sin tema';
$subtema_id = $_GET['subtema_id'] ?? 0;

$archivo = $_GET['archivo'] ?? 'NULO';


$nombreArchivo = obtenerNombreArchivoCsv($cuadro_id);
if ($nombreArchivo) {
    $ruta = "cuadro/uploads/$tema/csv/$nombreArchivo"; 
} else {
    echo "No se encontró el archivo CSV para el cuadro_id dado.";
}

if (!file_exists($ruta)) die("Archivo CSV no encontrado.");


$datos = obtenerDatosCuadro($cuadro_id);
if ($datos) {
    $titulo = $datos['codigo_cuadro'] . " - " . $datos['titulo'];
} else {
    echo "No se encontró el cuadro con ID dado.";
}



$csv = array_map('str_getcsv', file($ruta));
$headers = $csv[0];
$data = array_slice($csv, 1);
$columnas_numericas = [];
$columnas_texto = [];

foreach ($headers as $i => $header) {
    $header_limpio = trim($header);

    // Condición especial solo para el cuadro con cabecera fragmentada
    if ($nombreArchivo === '3.S.3_1pl3a3ee92') {
        // Solo aceptar columnas como "Defunciones 2013", "Porcentaje 2014", etc.
        if (!preg_match('/^(Defunciones|Porcentaje) \d{4}$/', $header_limpio)) {
            continue; // omitir cualquier otra columna
        }
    }

    $es_numerica = true;
    foreach ($data as $fila) {
        if (!isset($fila[$i])) continue;
        $valor = trim(str_replace([',', '%', ' '], '', $fila[$i]));
        if ($valor === '' || $valor === '-' || strtolower($valor) === 's/n') continue;
        if (!is_numeric($valor)) {
            $es_numerica = false;
            break;
        }
    }
    if ($es_numerica) {
        $columnas_numericas[$i] = $header_limpio;
    } else {
        $columnas_texto[$i] = $header_limpio;
    }
}

$columnas_numericas = [];
$columnas_texto = [];

foreach ($headers as $i => $header) {
    $es_numerica = true;
    foreach ($data as $fila) {
        if (!isset($fila[$i])) continue;
        $valor = trim(str_replace([',', '%', ' '], '', $fila[$i]));
        if ($valor === '' || $valor === '-' || strtolower($valor) === 's/n') continue;
        if (!is_numeric($valor)) {
            $es_numerica = false;
            break;
        }
    }
    if ($es_numerica) {
        $columnas_numericas[$i] = $header;
    } else {
        $columnas_texto[$i] = $header;
    }
}