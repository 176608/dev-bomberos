<?php
session_start();
include '../models/conexion.php';
include '../models/subtemaEstadistica_agregarModel.php';

$temas = obtenerTemas($conexion);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $subtema = $_POST["ce_subtema"];
    $tema = $_POST["ce_tema_id"];

    $nombreImagen = null; // Por defecto sin imagen

    if (isset($_POST['agregar_imagen']) && $_POST['agregar_imagen'] == '1') {
        if (isset($_FILES['imagen_subtema']) && $_FILES['imagen_subtema']['error'] === UPLOAD_ERR_OK) {
            // Validar que sea PNG
            $tipoMime = mime_content_type($_FILES['imagen_subtema']['tmp_name']);
            $extension = pathinfo($_FILES['imagen_subtema']['name'], PATHINFO_EXTENSION);

            if ($tipoMime === 'image/png' && strtolower($extension) === 'png') {
                $nombreImagen = basename($_FILES['imagen_subtema']['name']);
                $rutaDestino = __DIR__ . '/../public/imagenes/' . $nombreImagen;

                if (!is_dir(__DIR__ . '/../public/imagenes/')) {
                    mkdir(__DIR__ . '/../public/imagenes/', 0755, true);
                }

                move_uploaded_file($_FILES['imagen_subtema']['tmp_name'], $rutaDestino);
            } else {
                die("Solo se permiten imágenes en formato PNG.");
            }
        }
    }

    insertarSubtema($conexion, $subtema, $tema, $nombreImagen);

    header("Location: subtemaEstadistica.php");
    exit;
}
