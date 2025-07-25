<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario'] !== 'admin') {
    echo "<h3>❌ Acceso denegado.</h3><a href='javascript:history.back()'>Volver</a>";
    exit;
}

require_once '../models/editar_csvModel.php';

$archivo = basename($_GET['archivo'] ?? '');
$tema = $_GET['tema'] ?? 'general';
$ruta = "cuadro/uploads/$tema/csv/$archivo";

if (!$archivo) {
    die("❌ No se recibió el nombre del archivo CSV.");
}

if (!file_exists($ruta)) {
    die("❌ El archivo <code>$archivo</code> no existe en <code>cuadro/uploads/$tema/csv/</code>");
}

// Guardar datos si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['data']) && is_array($_POST['data'])) {
        $tema = $_POST['tema'] ?? 'general';
        $archivo = basename($_POST['archivo'] ?? '');
        $ruta = "cuadro/uploads/$tema/$archivo";

        guardarCSV($ruta, $_POST['data']);
        header("Location: $tema.php?exito=1");
        exit;
    }
}

// Leer los datos del archivo
$filas = leerCSV($ruta);
