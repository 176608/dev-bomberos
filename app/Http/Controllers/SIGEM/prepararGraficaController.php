<?php
require_once '../models/conexion.php';
require_once '../models/prepararGraficaModel.php';


$CuadroEstadistico = isset($_REQUEST['CuadroEstadistico']) ? intval($_REQUEST['CuadroEstadistico']) : 0;
$cuadro = obtenerCuadro($conexion, $CuadroEstadistico);
$csvs = obtenerCSVs($conexion, $CuadroEstadistico);

$EjeHorizontalOpciones = '';
$EjeVerticalOpciones = '';
$cellTotal = 1;

while ($csv = $csvs->fetch_assoc()) {
    $archivo = '../cuadro/uploads/' . $csv['nombre_archivo_csv'];
    if (!file_exists($archivo)) continue;

    $EjeHorizontalOpciones .= '<optgroup label="' . htmlspecialchars($csv['titulo']) . '">';
    $handle = fopen($archivo, "r");

    $row = 0;
    while (($data = fgetcsv($handle, 1000, ",")) !== false) {
        $cellnum = 0;
        foreach ($data as $cell) {
            $cell = utf8_encode($cell);
            if ($row == 0 && $cellnum > 0) {
                $EjeHorizontalOpciones .= "<option value=\"$cellTotal\">$cell</option>";
                $cellTotal++;
            }
            if ($row > 0 && $cellnum == 0) {
                $EjeVerticalOpciones .= "<option value=\"$row\">$cell</option>";
            }
            $cellnum++;
        }
        $row++;
    }

    $EjeHorizontalOpciones .= '</optgroup>';
    fclose($handle);
}