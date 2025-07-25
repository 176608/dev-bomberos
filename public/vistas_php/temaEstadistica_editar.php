<?php
include '../controllers/temaEstadistica_editarController.php';

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Tema Estadística</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="p-4">
    <h2>Editar Tema Estadística</h2>
    <form method="post">
        <div class="mb-3">
            <label for="tema" class="form-label">Nombre del Tema Estadística</label>
            <input type="text" name="tema" id="tema" class="form-control" value="<?= htmlspecialchars($tema['nombre']) ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="temaEstadistica.php" class="btn btn-secondary">Cancelar</a>
    </form>
</body>
</html>
