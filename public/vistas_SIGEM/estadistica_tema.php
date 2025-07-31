
<?php
// 🔓 Vista pública con contenido dinámico por AJAX.
// Muestra botones de subtemas y carga los cuadros estadísticos correspondientes sin requerir autenticación.
// No permite modificar información, solo consultar.
// Puede ser usada tanto por usuarios públicos como por admin.
require_once '../controllers/estadisticaTemaController.php';
?>
<?php
require_once '../controllers/estadisticaTemaController.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Geográfico - SIGEM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>
<div class="container mt-4">
    <h4 class="mb-4">Selecciona un subtema:</h4>

    <div class="row mb-4">
        <?php while ($sub = $subtemas->fetch_assoc()): ?>
            <div class="col-md-6 mb-3">
                <button class="btn btn-success w-100" onclick="cargarSubtema(<?= $sub['id'] ?>)">
                    <?= htmlspecialchars($sub['subtema_titulo']) ?>
                </button>
            </div>
        <?php endwhile; ?>
    </div>

    <!-- Aquí se cargarán los cuadros estadísticos -->
    <div id="contenidoSubtema"></div>
</div>

<script src="subtema.js"></script>
</body>
</html>
