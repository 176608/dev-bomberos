<?php
// Este script pertenece a una vista restringida del sistema SIGEM.
// Solo usuarios autorizados pueden editar cuadros estadísticos.

require_once '../controllers/sesionController.php';
include '../models/cuadroEstadistico_editarModel.php';

$temas = obtenerTemas($conexion);
$subtemas = obtenerSubtemas($conexion);

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $cuadro = obtenerCuadroPorId($conexion, $id); 
}

if (!$id) {
    die("ID inválido.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $subtemaNombre = $_POST["subtema"];
    $temaNombre  = $_POST["tema"];
    $codigo = $_POST['codigo_cuadro'] ?? '';
    $titulo = $_POST['nombre_cuadro'] ?? '';

    // Buscar el ID del tema según su nombre
    $temaId = null;
    foreach ($temas as $tema) {
        if ($tema['nombre'] === $temaNombre) {
            $temaId = $tema['id'];
            echo "<script>alert('Tema encontrado: ID = $temaId, Nombre = " . addslashes($temaNombre) . "');</script>";
            break;
        }
    }

    // Buscar el ID del subtema según su nombre
    $subtemaId = null;
    foreach ($subtemas as $sub) {
        if ($sub['nombre_subtema'] === $subtemaNombre) {
            $subtemaId = $sub['id'];
            echo "<script>alert('Subtema encontrado: ID = $subtemaId, Nombre = " . addslashes($subtemaNombre) . "');</script>";
            break;
        }
    }

    $actualizado = actualizarCuadroEstadistico($conexion, $id, $codigo, $titulo, $temaId, $subtemaId);

    if ($actualizado) {
        echo "<script>alert('Registro Actualizado.');</script>";
        header("Location: cuadroEstadistico.php");
        exit;
    } else {
        echo "<script>alert('No se pudo actualizar o no hubo cambios.');</script>";
    }
}
