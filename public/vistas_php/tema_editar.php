<?php
include '../controllers/tema_editarController.php';

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Tema</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="p-4">
    <h2>Editar Tema</h2>
    <form method="post">
        <div class="mb-3">
            <label for="tema" class="form-label">Nombre del Tema</label>
            <input type="text" name="tema" id="tema" class="form-control" value="<?= htmlspecialchars($tema['tema']) ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="tema.php" class="btn btn-secondary">Cancelar</a>
    </form>
</body>
</html>
