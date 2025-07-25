<?php
include '../controllers/subtemaEstadistica_agregarController.php';

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Subtema Estadística</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .top-bar {
            background-color: #2a6e48;
            color: white;
            padding: 5px 15px;
            font-size: 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo-bar {
            background-color: white;
            text-align: center;
            padding: 10px;
        }
        .logo-bar img {
            max-height: 90px;
            margin: 0 10px;
        }
        .main-nav {
            background-color: #2a6e48;
            padding: 8px 0;
            display: flex;
            justify-content: center;
            gap: 20px;
            border-bottom: 4px solid #ffd700;
        }
        .main-nav a {
            color: white;
            text-decoration: none;
            padding: 6px 14px;
            font-weight: bold;
        }
        .main-nav a:hover {
            background-color: rgba(255,255,255,0.2);
            border-radius: 4px;
        }
        .footer-logos {
            text-align: center;
            margin-top: 40px;
        }
        .footer-logos img {
            max-height: 60px;
            margin: 10px 25px;
        }
    </style>
</head>
<body>

<!-- Encabezado -->
<div class="top-bar">
    <div>Instituto Municipal de Investigación y Planeación</div>
    <div>Ciudad Juárez, Chihuahua</div>
</div>

<div class="logo-bar">
    <img src="imagenes/sige1.png" alt="IMIP">
    <img src="imagenes/sige2.png" alt="SIGEM">
</div>

<!-- Menú incluido -->
<?php include 'menuprincipal.php'; ?>
<div class="main-card"></div>

<!-- Contenido -->
<div class="container mt-4">
    <h2 class="mb-4">Agregar Nuevo Subtema Estadística</h2>

    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="ce_subtema" class="form-label">Nombre del Subtema</label>
            <input type="text" class="form-control" id="ce_subtema" name="ce_subtema" required>
        </div>
        <div class="mb-3">
            <label for="ce_tema_id" class="form-label">Tema Relacionado</label>
            <select class="form-select" id="ce_tema_id" name="ce_tema_id" required>
                <option value="">Selecciona un tema</option>
                <?php while ($row = $temas->fetch_assoc()): ?>
                    <option value="<?= $row['nombre'] ?>"><?= htmlspecialchars($row['nombre']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="row mb-3">
    <!-- Radio: ¿Agregar imagen? -->
    <div class="col-md-6">
        <label class="form-label">¿Agregar imagen al subtema?</label><br>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="agregar_imagen" id="imagen_si" value="1" checked>
            <label class="form-check-label" for="imagen_si">Sí</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="agregar_imagen" id="imagen_no" value="0">
            <label class="form-check-label" for="imagen_no">No</label>
        </div>
    </div>

    <!-- Input para imagen -->
    <div class="col-md-6">
        <label for="imagen_subtema" class="form-label">Imagen del subtema</label>
        <input type="file" class="form-control" id="imagen_subtema" name="imagen_subtema" accept=".png" required>
    </div>
</div>
        <button type="submit" class="btn btn-primary">Guardar</button>
        <a href="subtemaEstadistica.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<!-- Pie de página -->
<div class="footer-logos mt-5">
    <img src="imagenes/sige2.png" alt="SIGEM Footer Logo">
    <img src="imagenes/logosfinales2.png" alt="IMIP Footer Logo">
    <img src="imagenes/logoadmin.png" alt="Ciudad Juárez Footer Logo">
</div>
<script>
    const radioSi = document.getElementById('imagen_si');
    const radioNo = document.getElementById('imagen_no');
    const inputImg = document.getElementById('imagen_subtema');

    function toggleImageInput() {
        if (radioSi.checked) {
            inputImg.disabled = false;
            inputImg.required = true;
        } else {
            inputImg.disabled = true;
            inputImg.required = false;
            inputImg.value = ''; // limpia si cambia a "no"
        }
    }

    radioSi.addEventListener('change', toggleImageInput);
    radioNo.addEventListener('change', toggleImageInput);
    window.addEventListener('DOMContentLoaded', toggleImageInput); // iniciar correctamente
</script>
</body>
</html>
