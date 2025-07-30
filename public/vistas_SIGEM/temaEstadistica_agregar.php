<?php
// ========================================================
// Vista de ADMINISTRADOR - Agregar Tema de Estadística
// ========================================================
// Este archivo permite al usuario administrador registrar
// un nuevo tema dentro de la sección de Estadística del sistema.
// El controlador se encarga de gestionar la inserción en la base de datos.

include '../controllers/temaEstadistica_agregarController.php';
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Tema</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="p-4">
    <h2>Nuevo Tema Estadística</h2>
    <form method="post">
        <div class="mb-3">
            <label for="tema" class="form-label">Nombre del Tema Estadística</label>
            <input type="text" name="tema" id="tema" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Guardar</button>
        <a href="temaEstadistica.php" class="btn btn-secondary">Cancelar</a>
    </form>
</body>
</html>
