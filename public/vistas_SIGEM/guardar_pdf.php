<?php
// 🔒 Vista restringida para administrador
// Permite subir y reemplazar archivos PDF asociados a cuadros estadísticos
// Solo ejecutable si el usuario con sesión activa es 'admin'
?>
<?php

include '../models/conexion.php';
include '../models/guardar_pdfModel.php';

session_start();
$usuario = $_SESSION['usuario'] ?? 'Público';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $usuario === 'admin') {
    if (isset($_FILES['nuevo_pdf']) && $_FILES['nuevo_pdf']['error'] === UPLOAD_ERR_OK) {
        $cuadro_id = $_POST['cuadro_id'] ?? '';
        $tema = $_POST['tema'] ?? '';

        if (!is_numeric($cuadro_id)) {
            die("❌ ID de cuadro inválido.<br><a href='javascript:history.back()'>Volver</a>");
        }

        // 📁 Validar que sea PDF
        $archivo = $_FILES['nuevo_pdf'];
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        if ($extension !== 'pdf') {
            die("❌ Solo se permiten archivos PDF.");
        }

        $nombre_pdf_nuevo = $archivo['name'];
        $ruta_base = __DIR__ . "/cuadro/uploads/$tema";
        $ruta_pdf_nueva = "$ruta_base/pdf/$nombre_pdf_nuevo";

        // 📁 Crear carpetas si no existen
        if (!file_exists("$ruta_base/pdf")) mkdir("$ruta_base/pdf", 0777, true);
        if (!file_exists("$ruta_base/respaldo_pdf")) mkdir("$ruta_base/respaldo_pdf", 0777, true);

        // 📤 Buscar si ya hay un archivo PDF anterior
        $nombre_pdf_anterior = obtenerNombrePdfActual($conexion, $cuadro_id);

        if ($nombre_pdf_anterior) {
            $ruta_anterior = "$ruta_base/pdf/$nombre_pdf_anterior";
            if (file_exists($ruta_anterior)) {
                $timestamp = date('Ymd_His');
                $nombre_respaldo = preg_replace('/\.pdf$/i', "_$timestamp.pdf", $nombre_pdf_anterior);
                $ruta_respaldo = "$ruta_base/respaldo_pdf/$nombre_respaldo";

                rename($ruta_anterior, $ruta_respaldo);
            }
        }

        // ✅ Guardar nuevo archivo
        if (move_uploaded_file($archivo['tmp_name'], $ruta_pdf_nueva)) {
            // Actualiza el nombre en la base de datos
            $resultado = guardarNombrePdf($conexion, $cuadro_id, $nombre_pdf_nuevo);

            if ($resultado) {
                echo "<script>
                    alert('✅ PDF actualizado correctamente como ' + " . json_encode($nombre_pdf_nuevo) . ");
                    window.location.href = " . json_encode($tema . ".php") . ";
                </script>";
            } else {
                echo "❌ Error al actualizar el nombre del archivo PDF en la base de datos.";
            }
        } else {
            echo "❌ Error al guardar el archivo.";
        }

        $conexion->close();
    } else {
        echo "❌ No se seleccionó un archivo PDF válido.";
    }
} else {
    echo "❌ Acceso denegado.";
}
?>
