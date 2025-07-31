<?php
// ===========================================================
// VISTA DE ADMINISTRADOR - Edición de subtema estadístico
// Esta vista permite modificar un subtema existente,
// por lo tanto, debe estar protegida para que solo el
// usuario con sesión 'admin' pueda acceder.
// ===========================================================

include '../controllers/subtemaEstadistica_editarController.php';
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Subtema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .top-bar { background-color: #2a6e48; color: white; padding: 5px 15px; font-size: 14px; display: flex; justify-content: space-between; align-items: center; }
        .logo-bar { background-color: white; text-align: center; padding: 10px; }
        .logo-bar img { max-height: 90px; margin: 0 10px; }
        .main-nav { background-color: #2a6e48; padding: 8px 0; display: flex; justify-content: center; gap: 20px; border-bottom: 4px solid #ffd700; }
        .main-nav a { color: white; text-decoration: none; padding: 6px 14px; font-weight: bold; }
        .main-nav a:hover { background-color: rgba(255,255,255,0.2); border-radius: 4px; }
        .footer-logos { text-align: center; margin-top: 40px; }
        .footer-logos img { max-height: 60px; margin: 10px 25px; }
    </style>
</head>
<body>

<div class="top-bar">
    <div>Instituto Municipal de Investigación y Planeación</div>
    <div>Ciudad Juárez, Chihuahua</div>
</div>

<div class="logo-bar">
    <img src="imagenes/sige1.png" alt="IMIP">
    <img src="imagenes/sige2.png" alt="SIGEM">
</div>

<?php include 'menuprincipal.php'; ?>
<div class="main-card"></div>

<div class="container mt-4">
    <h2 class="mb-4">Editar Subtema</h2>

    <form method="post">
        <div class="mb-3">
            <label for="ce_subtema" class="form-label">Nombre del Subtema</label>
            <input type="text" class="form-control" id="ce_subtema" name="ce_subtema" value="<?= htmlspecialchars($subtemaActual['nombre_subtema']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="ce_tema" class="form-label">Tema Relacionado</label>
            <select class="form-select" id="ce_tema" name="ce_tema" required>
                <option value="">-- Selecciona un nuevo tema --</option>
                <?php while ($row = $temas->fetch_assoc()): ?>
                    <option value="<?= $row['nombre'] ?>">
                        <?= htmlspecialchars($row['nombre']) ?>
                    </option>
                <?php endwhile; ?>
            </select>

        </div>
        <button type="submit" class="btn btn-primary">Guardar cambios</button>
        <a href="subtemaEstadistica.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<div class="footer-logos">
    <img src="imagenes/logosfinales2.png" alt="IMIP">
    <img src="imagenes/logoadmin.png" alt="Gobierno Municipal">
    <img src="imagenes/sige2.png" alt="SIGEM">
</div>

</body>
</html>
