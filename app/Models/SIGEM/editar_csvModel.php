<?php
function leerCSV($ruta) {
    $filas = [];
    if (($handle = fopen($ruta, "r")) !== false) {
        while (($data = fgetcsv($handle)) !== false) {
            $filas[] = $data;
        }
        fclose($handle);
    }
    return $filas;
}

function guardarCSV($ruta, $data) {
    $fp = fopen($ruta, 'w');
    foreach ($data as $row) {
        fputcsv($fp, $row);
    }
    fclose($fp);
}
