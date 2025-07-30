
<?php
// 🔓 Vista pública con funciones ampliadas condicionales para el usuario 'admin'.
// Muestra los cuadros estadísticos del tema Económico por subtema.
// Todos pueden consultar la información, pero solo el administrador puede ver y acceder a los botones de actualización de archivos.
require_once '../controllers/EconomicoController.php';
?>
<?php require_once '../controllers/EconomicoController.php'; ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Económico - SIGEM</title>
    <link rel="icon" type="image/png" href="imagenes/imiplogo_200x200_trans.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #eeeeee; font-family: Arial, sans-serif; }
        .top-bar { background-color: #2a6e48; color: white; padding: 5px 15px; font-size: 14px; display: flex; justify-content: space-between; align-items: center; }
        .right-section { display: flex; align-items: center; gap: 15px; }
        .user-icon { width: 28px; height: 28px; background-color: white; color: #2a6e48; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        .header-logos { display: flex; align-items: center; gap: 20px; margin: 10px 0 10px 10px; }
        .header-logos img:first-child { height: 85px; }
        .header-logos img:last-child { height: 65px; }
        .main-card { background-color: white; border-radius: 8px; padding: 20px; margin: 0 auto; width: 95%; box-shadow: 0 0 10px rgba(0,0,0,0.1); }

        .titulo-cuadro {
            font-size: 16px;
            font-weight: bold;
            color: #006633;
            margin-top: 20px;
            text-align: center;
            cursor: pointer;
        }

        .titulo-cuadro:hover { text-decoration: underline; }

        .descargas { text-align: center; margin-bottom: 10px; }
        .descargas a { margin: 0 10px; }
        .table-container { overflow-x: auto; width: 100%; }
        .table-sigem { font-size: 12px; border-collapse: collapse; width: max-content; min-width: 100%; margin-bottom: 20px; }
        .table-sigem thead { background-color: #d6e9c6; color: #3c763d; text-align: center; }
        .table-sigem tbody tr:nth-child(even) { background-color: #f9f9f9; }
        .table-sigem th, .table-sigem td {
            border: 1px solid #ccc;
            padding: 6px 10px;
            text-align: center;
            max-width: 300px;
            word-break: break-word;
        }
        .footer-logos { display: flex; justify-content: center; align-items: center; margin-top: 40px; padding-top: 20px; gap: 60px; border-top: 1px solid #ccc; }
        .footer-logos img:first-child { height: 70px; }
        .footer-logos img:nth-child(2) { height: 90px; }
        .footer-logos img:nth-child(3) { height: 80px; }
        .sidebar-icon { text-align: center; text-decoration: none; color: black; }
        .sidebar-icon img { max-height: 60px; }
        .sidebar-icon p { font-size: 13px; font-weight: bold; margin: 5px 0 0; }
    </style>
</head>
<body>
<div class="top-bar">
    <div>Instituto Municipal de Investigación y Planeación</div>
    <div class="right-section">
        <div>Ciudad Juárez, Chihuahua</div>
        <div class="user-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#2a6e48" class="bi bi-person" viewBox="0 0 16 16">
                <path d="M10 5a2 2 0 1 1-4 0 2 2 0 0 1 4 0z"/>
                <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-6a6 6 0 0 0-4.472 10.118C4.723 11.29 6.299 11 8 11s3.277.29 4.472.618A6 6 0 0 0 8 2z"/>
            </svg>
        </div>
        <div><strong><?php echo htmlspecialchars($usuario); ?></strong></div>
        <?php if ($usuario !== 'Público'): ?>
            <div><a href="logout.php" class="btn btn-sm btn-light text-success">Cerrar sesión</a></div>
        <?php else: ?>
            <div><a href="login.php" class="btn btn-sm btn-light text-success">Iniciar sesión</a></div>
        <?php endif; ?>
    </div>
</div>

<div class="header-logos">
    <img src="imagenes/sige1.png" alt="IMIP Logo">
    <img src="imagenes/sige2.png" alt="SIGEM Logo">
</div>

<?php include 'menuprincipal.php'; ?>

<div class="main-card">
    <div class="row">
        <div class="col-md-2">
            <div class="text-center mb-3 mt-2">
                <img src="imagenes/poblacioneconomico.png" alt="Económico" style="height: 55px;">
                <h5 class="mt-2 text-success">Económico</h5>
            </div>
                    <div class="d-flex flex-column gap-3 align-items-center">
            <?php foreach ($subtemas as $subtema): ?>
                <a href="<?= htmlspecialchars($tema_id) ?>.php?subtema_id=<?= htmlspecialchars($subtema['id']) ?>" class="sidebar-icon">
                    <img src="imagenes/<?= htmlspecialchars($subtema['imagen']) ?>" alt="<?= htmlspecialchars($subtema['nombre_subtema']) ?>">
                    <p><?= htmlspecialchars($subtema['nombre_subtema']) ?></p>
                </a>
            <?php endforeach; ?>
            </div>
        </div>

        <div class="col-md-8 mx-auto">
            <h4 class="text-success text-center">Selecciona un tema:</h4>
            <div class="text-center mb-4">
            <select id="temaSelect" class="form-select w-75 mx-auto">
                    <?php
                    if ($temas && mysqli_num_rows($temas) > 0) {
                        while ($fila = mysqli_fetch_assoc($temas)) {
                            $selected = ($fila['nombre_archivo'] === 'economico.php') ? 'selected' : '';
                            echo "<option value=\"{$fila['nombre_archivo']}\" $selected>{$fila['nombre']}</option>";
                        }
                    } else {
                        echo '<option value="">No hay temas disponibles</option>';
                    }
                    ?>
                </select>
            </div>

            <h5 class="text-center text-success mb-4">Subtema: <?= htmlspecialchars($nombre_actual); ?></h5>
            <div class="col-md-9" id="contenedorCuadros" style="backhrpi">
                <?php foreach ($cuadros as $index => $cuadro): ?>
                    <h4 class="titulo-cuadro toggle-titulo" data-target="contenedor-<?php echo $index; ?>" style="text-align:justify;">
                        <?php echo htmlspecialchars($cuadro['codigo_cuadro']) . ' - ' . htmlspecialchars($cuadro['cuadro_estadistico_titulo']); ?>
                    </h4>
                    <div id="contenedor-<?php echo $index; ?>" style="display:none;">
                        <div class="descargas mb-2" style="display:flex; justify-content:center; gap:8px; flex-wrap:wrap; align-items:center;">
                            <?php if (!empty($cuadro['pdf_file'])): ?>
                            <a href="cuadro/uploads/<?= urlencode($tema_id) . '/pdf/' . htmlspecialchars($cuadro['pdf_file']) ?>" class="btn btn-outline-danger btn-sm" target="_blank">📄 PDF</a>
                            <?php endif; ?>

                            <?php if ($cuadro['permite_grafica'] == 1): ?>
                                <a href="generarGrafica.php?cuadro_id=<?= pathinfo($cuadro['cuadro_estadistico_id'], PATHINFO_FILENAME); ?>&tema=<?= urlencode($tema_id) ?>" class="btn btn-outline-success btn-sm" target="_blank">📊 Gráfica</a>
                            <?php endif; ?>

                            <?php if ($usuario === 'admin'): ?>
                                <a href="subir_pdf.php?id=<?= $cuadro['cuadro_estadistico_id'] ?>&tema=<?= $tema_id ?>" class="btn btn-outline-primary btn-sm">📤 Actualizar PDF</a>
                                <a href="subir_csv.php?id=<?= $cuadro['cuadro_estadistico_id'] ?>&tema=<?= $tema_id ?>" class="btn btn-outline-primary btn-sm">📥 Subir nuevo CSV</a>
                                <a href="editar_csv.php?archivo=<?= urlencode($cuadro['nombe_archivo_csv']); ?>&tema=<?= $tema_id ?>" class="btn btn-outline-dark btn-sm">✏️ Editar CSV</a>
                            <?php endif; ?>

                        </div>
                        <?php
$codigo = $cuadro['codigo_cuadro'];
$rutaCSV = "cuadro/uploads/$tema_id/csv/" . $cuadro['nombe_archivo_csv'];

if ($codigo === '5.ECO.1' || $codigo === '5.ECO.2') {
    // No mostrar CSV ni alerta
    if (file_exists($rutaCSV)) {
        echo "<div class='descargas mb-2' style='display:flex; justify-content:left; gap:8px; flex-wrap:wrap; align-items:center;'>";
        mostrarCSV($rutaCSV);
        echo "</div>";
    }
} else {
    echo "<div class='descargas mb-2' style='display:flex; justify-content:left; gap:8px; flex-wrap:wrap; align-items:center;'>";
    mostrarCSV($rutaCSV);
    echo "</div>";
}
?>
     
                    </div>
                <?php endforeach; ?>
        </div>
<script>
document.getElementById('temaSelect').addEventListener('change', function() {
    const valor = this.value;
    if (valor) {
        window.location.href = valor; 
    }
});

document.querySelectorAll('.toggle-titulo').forEach(function(titulo) {
    titulo.addEventListener('click', function() {
        const targetId = this.dataset.target;
        const contenedor = document.getElementById(targetId);

        // Si el contenedor ya está visible, ciérralo
        if (contenedor.style.display === "block") {
            contenedor.style.display = "none";
            return;
        }

        // Cierra todos los divs asociados a los títulos
        document.querySelectorAll('.toggle-titulo').forEach(function(t) {
            const otherId = t.dataset.target;
            const otherContenedor = document.getElementById(otherId);
            if (otherContenedor) {
                otherContenedor.style.display = "none";
            }
        });

        // Abre solo el clickeado
        contenedor.style.display = "block";
    });
});
</script>

        </div>
    </div>
</div>

<div class="footer-logos mt-5">
    <img src="imagenes/sige2.png" alt="SIGEM Footer Logo">
    <img src="imagenes/logosfinales2.png" alt="IMIP Footer Logo">
    <img src="imagenes/logoadmin.png" alt="Ciudad Juárez Footer Logo">
</div>

</body>
</html>
