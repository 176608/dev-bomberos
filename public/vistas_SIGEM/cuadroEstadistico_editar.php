<?php
// ⚙️ Vista privada de administración.
// Esta página permite editar cuadros estadísticos (cambiar su nombre, código o ubicación de tema/subtema).
// Solo debe estar accesible para el usuario 'admin'.
// Se recomienda validar sesión y rol antes de mostrar el formulario.
include '../controllers/cuadroEstadistico_editarController.php';
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
        <div class="row mb-3">
            <div class="col-md-2">
                <label for="codigo_cuadro" class="form-label">Código</label>
                <input type="text" class="form-control" id="codigo_cuadro" name="codigo_cuadro"
                    value="<?= htmlspecialchars($cuadro['codigo_cuadro'] ?? '') ?>" required>            
            </div>


            <div class="col-md-9">
                    <label for="nombre_cuadro" class="form-label">Nombre del Cuadro Estadístico</label>
                <input type="text" class="form-control" id="nombre_cuadro" name="nombre_cuadro"
                    value="<?= htmlspecialchars($cuadro['cuadro_estadistico_titulo'] ?? '') ?>" required>            
            </div>

        </div>

 <div class="row">
    <!-- Columna izquierda: ubicación actual -->
    <div class="col-md-6">
        <h5>Ubicación actual</h5>
        <div class="mb-3">
            <label class="form-label">Tema Actual</label>
            <input type="text" class="form-control" id="tema_anterior" value="<?php
                foreach ($temas as $tema) {
                    if ($tema['id'] == $cuadro['tema_id']) {
                        echo htmlspecialchars($tema['nombre']);
                        break;
                    }
                }
            ?>" disabled>        </div>
        <div class="mb-3">
            <label class="form-label">Subtema Actual</label>
            <input type="text" class="form-control" id="subtema_anterior" value="<?php
                foreach ($subtemas as $subtema) {
                    if ($subtema['id'] == $cuadro['subtema_id']) {
                        echo htmlspecialchars($subtema['nombre_subtema']);
                        break;
                    }
                }
            ?>" disabled>        </div>
    </div>

    <!-- Columna derecha: nueva ubicación -->
    <div class="col-md-6">
        <h5>Nueva ubicación</h5>
        <div class="mb-3">
            <label for="tema" class="form-label">Tema</label>
            <select class="form-select" id="tema" name="tema" required>
                <option value="">Selecciona un tema</option>
                <?php foreach ($temas as $tema): ?>
                    <option value="<?= htmlspecialchars($tema['nombre']) ?>"><?= htmlspecialchars($tema['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="subtema" class="form-label">Subtema</label> 
            <select class="form-select" id="subtema" name="subtema" required disabled>
                <option value="">Selecciona un tema</option>
                <?php foreach ($subtemas as $sub): ?>
                    <option value="<?= htmlspecialchars($sub['nombre_subtema']) ?>" data-tema="<?= htmlspecialchars($sub['tema']) ?>">
                        <?= htmlspecialchars($sub['nombre_subtema']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        </div>
    </div>
            <button type="submit" class="btn btn-primary">Guardar cambios</button>
        <a href="cuadroEstadistico.php" class="btn btn-secondary">Cancelar</a>
</div>


    </form>
</div>

<div class="footer-logos">
    <img src="imagenes/logosfinales2.png" alt="IMIP">
    <img src="imagenes/logoadmin.png" alt="Gobierno Municipal">
    <img src="imagenes/sige2.png" alt="SIGEM">
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const temaSelect = document.getElementById('tema');
    const subtemaSelect = document.getElementById('subtema');

    temaSelect.addEventListener('change', function () {
        const temaSeleccionado = this.value;

        // Si no hay tema seleccionado, deshabilitar y limpiar subtemas
        if (!temaSeleccionado) {
            subtemaSelect.disabled = true;
            subtemaSelect.value = '';
            Array.from(subtemaSelect.options).forEach(option => {
                if (option.value !== '') option.hidden = true;
            });
            return;
        }

        // Si hay tema, habilitar subtemas y filtrar por data-tema
        subtemaSelect.disabled = false;
        Array.from(subtemaSelect.options).forEach(option => {
            if (option.value === '') {
                option.hidden = false; // mostrar "Seleccione un subtema"
            } else {
                option.hidden = option.dataset.tema !== temaSeleccionado;
            }
        });

        // Resetear selección
        subtemaSelect.value = '';
    });
});
</script>
</body>
</html>
