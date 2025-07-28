<?php
// Este script gestiona una vista restringida del sistema SIGEM.
// Solo usuarios con permisos pueden agregar cuadros estadísticos.

session_start();
include '../models/conexion.php';
include '../models/cuadroEstadistico_agregarModel.php';

$temas = obtenerTemas($conexion);
$subtemas = obtenerSubtemas($conexion);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $codigo_cuadro = $_POST['codigo_cuadro'] ?? '';
    $nombre_cuadro = $_POST['nombre_cuadro'] ?? '';
    $temaPost = $_POST['tema'] ?? '';
    $subtemaPost = $_POST['subtema'] ?? '';
/*
    $archivo_pdf = null;
    $archivo_csv = null;
    
    // Subir archivo PDF si aplica
    if (!empty($_FILES['archivo_pdf']['name'])) {
        $archivo_pdf = basename($_FILES['archivo_pdf']['name']);
        move_uploaded_file($_FILES['archivo_pdf']['tmp_name'], '../uploads/' . $archivo_pdf);
    }

    // Subir archivo CSV si aplica
    if (!empty($_FILES['archivo_csv']['name'])) {
        $archivo_csv = basename($_FILES['archivo_csv']['name']);
        move_uploaded_file($_FILES['archivo_csv']['tmp_name'], '../uploads/' . $archivo_csv);
    }*/
    // Buscar el ID del tema a partir del nombre recibido
    $tema_id = null;
    foreach ($temas as $tema) {
        if ($tema['nombre'] === $temaPost) {
            $tema_id = $tema['id'];
            break;
        }
    }

    // Buscar el ID del subtema a partir del nombre recibido
    $subtema_id = null;
    foreach ($subtemas as $subtema) {
        if ($subtema['nombre_subtema'] === $subtemaPost) {
            $subtema_id = $subtema['id'];
            break;
        }
    }

    // Insertar a la base de datos usando la función del modelo
    //$resultado = insertar_cuadro_estadistico($conexion, $nombre_cuadro, $tema_id, $subtema_id, $archivo_pdf, $archivo_csv);
    $resultado = insertar_cuadro_estadistico($conexion, $codigo_cuadro, $nombre_cuadro, $tema_id, $subtema_id);

if ($resultado) {
    echo "<script>
            alert('Insertado correctamente.');
            window.location.href = 'cuadroEstadistico.php';
          </script>";
} else {
    echo "<script>
            alert('Error al insertar.');
            window.location.href = 'cuadroEstadistico.php';
          </script>";
}   
}
