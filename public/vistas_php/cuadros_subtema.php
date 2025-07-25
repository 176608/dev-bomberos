<?php
require_once '../models/conexion.php';

if (!isset($_POST['subtema_id'])) {
    echo '<div class="alert alert-danger">Subtema no especificado.</div>';
    exit;
}

$subtema_id = intval($_POST['subtema_id']);

// Consulta ordenada por título
$cuadros = $conexion->query("SELECT * FROM cuadro_estadistico WHERE subtema_id = $subtema_id ORDER BY cuadro_estadistico_titulo ASC");

if (!$cuadros || $cuadros->num_rows === 0) {
    echo '<div class="alert alert-warning">No hay cuadros estadísticos para este subtema.</div>';
    exit;
}

while ($c = $cuadros->fetch_assoc()):
    echo '<div class="mb-5 border p-3">';
    echo '<h5>' . htmlspecialchars($c['cuadro_estadistico_titulo']) . '</h5>';

    // Botón PDF si existe
    if (!empty($c['pdf_file'])) {
        echo '<a href="cuadro/uploads/' . htmlspecialchars($c['pdf_file']) . '" target="_blank" class="btn btn-danger btn-sm me-2">Descargar PDF</a>';
    }

    // Botón gráfico (placeholder)
    echo '<button class="btn btn-info btn-sm mb-3" onclick="alert(\'Función de gráfica aún no implementada\')">Ver gráfica</button>';

    // Buscar archivos CSV del cuadro
    $cuadro_id = $c['cuadro_estadistico_id'];
    $csvs = $conexion->query("SELECT * FROM cuadro_estadistico_csv WHERE cuadro_estadistico_id = $cuadro_id");

    if ($csvs && $csvs->num_rows > 0) {
        while ($csv = $csvs->fetch_assoc()):
            echo '<h6 class="mt-3">' . htmlspecialchars($csv['titulo']) . '</h6>';

            // CUIDADO: usa la columna correcta 'nombe_archivo_csv'
            if (isset($csv['nombe_archivo_csv']) && !empty($csv['nombe_archivo_csv'])) {
                $archivo_csv = 'cuadro/uploads/' . $csv['nombe_archivo_csv'];

                if (file_exists($archivo_csv)) {
                    echo '<div class="table-responsive"><table class="table table-bordered table-sm">';
                    if (($handle = fopen($archivo_csv, "r")) !== false) {
                        $firstRow = true;
                        while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                            echo $firstRow ? "<thead><tr>" : "<tr>";
                            foreach ($data as $cell) {
                                echo $firstRow ? "<th>" . htmlspecialchars($cell) . "</th>" : "<td>" . htmlspecialchars($cell) . "</td>";
                            }
                            echo $firstRow ? "</tr></thead><tbody>" : "</tr>";
                            $firstRow = false;
                        }
                        echo "</tbody></table></div>";
                        fclose($handle);
                    } else {
                        echo '<div class="alert alert-danger">Error al abrir el archivo CSV.</div>';
                    }
                } else {
                    echo '<div class="alert alert-warning">Archivo CSV no encontrado: ' . htmlspecialchars($archivo_csv) . '</div>';
                }
            } else {
                echo '<div class="alert alert-warning">No se especificó el nombre del archivo CSV.</div>';
            }
        endwhile;
    } else {
        echo '<div class="alert alert-warning">No hay archivos CSV para este cuadro.</div>';
    }

    echo '</div>';
endwhile;
?>
