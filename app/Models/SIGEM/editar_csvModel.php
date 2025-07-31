<?php
// USO COMPARTIDO — Este archivo contiene funciones para leer y guardar archivos CSV.
// Puede ser utilizado tanto en vistas públicas (para mostrar datos) como en vistas de administrador (para editar/guardar).
// La protección de acceso dependerá del archivo que lo incluya.

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
