<?php
include '../controllers/socioDemograficoController.php'

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sociodemográfico - SIGEM</title>
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
        .titulo-cuadro { font-size: 16px; font-weight: bold; color: #006633; text-align: center; margin-top: 20px; cursor: pointer; }
        .titulo-cuadro:hover { text-decoration: underline; }
        .descargas { text-align: center; margin-bottom: 10px; }
        .descargas a { margin: 0 10px; }
        .table-sigem { font-size: 13px; border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        .table-sigem thead { background-color: #d6e9c6; color: #3c763d; text-align: center; }
        .table-sigem tbody tr:nth-child(even) { background-color: #f9f9f9; }
        .table-sigem th, .table-sigem td { border: 1px solid #ccc; padding: 6px; text-align: center; }
        .footer-logos { display: flex; justify-content: center; align-items: center; margin-top: 40px; padding-top: 20px; gap: 60px; border-top: 1px solid #ccc; }
        .footer-logos img:first-child { height: 70px; }
        .footer-logos img:nth-child(2) { height: 90px; }
        .footer-logos img:nth-child(3) { height: 80px; }
        .sidebar-icon { text-align: center; width: 100%; text-decoration: none; color: black; }
        .sidebar-icon img { max-height: 60px; }
        .sidebar-icon p { font-size: 13px; font-weight: bold; margin: 5px 0 0; }

        /* AGREGADO PARA DESBORDAMIENTO */
        .table-responsive {
            overflow-x: auto;
            width: 100%;
        }
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

        <div class="col-md-9 mx-auto" >
            <h4 class="text-center text-success mb-4">Selecciona un tema:</h4>
            <div class="text-center mb-4">
                 <select id="temaSelect" class="form-select w-75 mx-auto">
                    <?php
                    if ($temas && mysqli_num_rows($temas) > 0) {
                        while ($fila = mysqli_fetch_assoc($temas)) {
                            $selected = ($fila['nombre_archivo'] === 'sociodemografico.php') ? 'selected' : '';
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
    <?php
    $codigo = $cuadro['codigo_cuadro'];
    $solo_pdf = in_array($codigo, ['3.SOP.8', '3.SOP.9', '3.SOP.10']);
    ?>
    
    <h4 class="titulo-cuadro toggle-titulo" data-target="contenedor-<?php echo $index; ?>" style="text-align:justify;">
        <?php echo htmlspecialchars($codigo) . ' - ' . htmlspecialchars($cuadro['cuadro_estadistico_titulo']); ?>
    </h4>

    <div id="contenedor-<?php echo $index; ?>" style="display:none;">
        <!-- Botones -->
        <div class="descargas mb-2" style="display:flex; justify-content:center; gap:8px; flex-wrap:wrap; align-items:center;">
            <?php if (!empty($cuadro['pdf_file'])): ?>
                <a href="cuadro/uploads/<?= urlencode($tema_id) . '/pdf/' . htmlspecialchars($cuadro['pdf_file']) ?>" class="btn btn-outline-danger btn-sm" target="_blank">📄 PDF</a>
            <?php endif; ?>

          <?php if ($cuadro['permite_grafica'] == 1): ?>
                                <a href="generarGrafica.php?cuadro_id=<?= pathinfo($cuadro['cuadro_estadistico_id'], PATHINFO_FILENAME); ?>&tema=<?= urlencode($tema_id) ?>&subtema_id=<?= urlencode($subtema_id) ?>" 
                                class="btn btn-outline-success btn-sm">
                                    📊 Gráfica
                                </a>
                            <?php endif; ?>

            <?php if ($usuario === 'admin'): ?>
                <a href="subir_pdf.php?id=<?= $cuadro['cuadro_estadistico_id'] ?>&tema=<?= $tema_id ?>" class="btn btn-outline-primary btn-sm">📤 Actualizar PDF</a>
                
                <?php if (!$solo_pdf): ?>
                    <a href="subir_csv.php?id=<?= $cuadro['cuadro_estadistico_id'] ?>&tema=<?= $tema_id ?>" class="btn btn-outline-primary btn-sm">📥 Subir nuevo CSV</a>
                    <?php if (!empty($cuadro['nombe_archivo_csv']) && file_exists("cuadro/uploads/$tema_id/csv/" . $cuadro['nombe_archivo_csv'])): ?>
                        <a href="editar_csv.php?archivo=<?= urlencode($cuadro['nombe_archivo_csv']); ?>&tema=<?= $tema_id ?>" class="btn btn-outline-dark btn-sm">✏️ Editar CSV</a>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <!-- Tabla CSV solo si no es uno de los códigos restringidos -->
        <?php if (!$solo_pdf): ?>
            <?php
            $csv_path = "cuadro/uploads/$tema_id/csv/" . $cuadro['nombe_archivo_csv'];
            if (!empty($cuadro['nombe_archivo_csv']) && file_exists($csv_path)):
            ?>
                <div style="overflow-x:auto; width:100%; padding-bottom:10px;">
                    <div class="table-responsive">
                        <?php mostrarCSV($csv_path); ?>
                    </div>
                </div>
            <?php elseif (!empty($cuadro['nombe_archivo_csv'])): ?>
                <div class="alert alert-warning text-center">Archivo CSV no encontrado: <?= htmlspecialchars($csv_path); ?></div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
<?php endforeach; ?>


        </div>
    </div>
        </div>
</div>

<div class="footer-logos mt-5">
    <img src="imagenes/sige2.png" alt="SIGEM Footer Logo">
    <img src="imagenes/logosfinales2.png" alt="IMIP Footer Logo">
    <img src="imagenes/logoadmin.png" alt="Ciudad Juárez Footer Logo">
</div>

<script>
document.getElementById("temaSelect").addEventListener("change", function () {
    const destino = this.value;
    if (destino) window.location.href = destino;
});

document.querySelectorAll('.toggle-titulo').forEach(function(titulo) {
    titulo.addEventListener('click', function() {
        const targetId = this.dataset.target;
        const contenedorActual = document.getElementById(targetId);

        const estaVisible = contenedorActual.style.display === "block";

        // Oculta todos los contenedores
        document.querySelectorAll('[id^="contenedor-"]').forEach(function(c) {
            c.style.display = "none";
        });

        // Solo lo muestra si no estaba visible
        if (!estaVisible) {
            contenedorActual.style.display = "block";
        }
        // Si estaba visible, ya lo ocultó al cerrar todos
    });
});
</script>


</script>

</body>
</html>
