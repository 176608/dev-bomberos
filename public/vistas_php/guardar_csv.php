<?php

include '../models/conexion.php';
include '../models/guardar_csvModel.php';

session_start();
$usuario = $_SESSION['usuario'] ?? 'Público';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $usuario === 'admin') {
    if (isset($_FILES['nuevo_csv']) && $_FILES['nuevo_csv']['error'] === UPLOAD_ERR_OK) {
        $cuadro_id = $_POST['cuadro_id'] ?? '';
        $tema = $_POST['tema'] ?? '';

        if (!is_numeric($cuadro_id)) {
            die("❌ ID de cuadro inválido.<br><a href='javascript:history.back()'>Volver</a>");
        }

        // 📁 Validar que sea CSV
        $archivo = $_FILES['nuevo_csv'];
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        if ($extension !== 'csv') {
            die("❌ Solo se permiten archivos CSV.");
        }

        $nombre_csv_nuevo = $archivo['name'];
        $ruta_base = __DIR__ . "/cuadro/uploads/$tema";
        $ruta_csv_nueva = "$ruta_base/csv/$nombre_csv_nuevo";

        // 📁 Crear carpetas si no existen
        if (!file_exists("$ruta_base/csv")) mkdir("$ruta_base/csv", 0777, true);
        if (!file_exists("$ruta_base/respaldo_csv")) mkdir("$ruta_base/respaldo_csv", 0777, true);

        // 📤 Buscar si ya hay un archivo anterior
        $nombre_csv_anterior = obtenerNombreCsvActual($conexion, $cuadro_id);


        if ($nombre_csv_anterior) {
            $ruta_anterior = "$ruta_base/csv/$nombre_csv_anterior";
            if (file_exists($ruta_anterior)) {
                $timestamp = date('Ymd_His');
                $nombre_respaldo = preg_replace('/\.csv$/i', "_$timestamp.csv", $nombre_csv_anterior);
                $ruta_respaldo = "$ruta_base/respaldo_csv/$nombre_respaldo";

                rename($ruta_anterior, $ruta_respaldo);
            }
        }

        // ✅ Guardar nuevo archivo
        if (move_uploaded_file($archivo['tmp_name'], $ruta_csv_nueva)) {
            // Actualiza el nombre en la base de datos
            $resultado = guardarNombreCsv($conexion, $cuadro_id, $nombre_csv_nuevo);

            if ($resultado) {
                echo "<script>
                    alert('✅ CSV actualizado correctamente como ' + " . json_encode($nombre_csv_nuevo) . ");
                    window.location.href = " . json_encode($tema . ".php") . ";
                </script>";
            } else {
                echo "❌ Error al actualizar el nombre del archivo CSV en la base de datos.";
            }
        } else {
            echo "❌ Error al guardar el archivo.";
        }

        $conexion->close();
    } else {
        echo "❌ No se seleccionó un archivo CSV válido.";
    }
} else {
    echo "❌ Acceso denegado.";
}
?>
