<?php 

include '../controllers/densidadController.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo $tituloPagina; ?> - SIGEM</title>
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
        .main-card { background-color: white; border-radius: 8px; padding: 20px; margin: 0 auto; width: 90%; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .table-sigem { font-size: 13px; border-collapse: collapse; max-width: 900px; margin: 20px auto; }
        .table-sigem thead { background-color: #d6e9c6; color: #3c763d; text-align: center; }
        .table-sigem tbody tr:nth-child(even) { background-color: #f9f9f9; }
        .table-sigem th, .table-sigem td { border: 1px solid #ccc; padding: 6px; text-align: center; }
        .titulo-cuadro {
            font-size: 16px;
            font-weight: bold;
            color: #006633;
            text-align: center;
            margin-top: 20px;
            cursor: pointer;
            text-decoration: none;
        }
        .titulo-cuadro:hover {
            text-decoration: underline;
        }
        .descargas { text-align: right; margin-right: 40px; }
        .descargas a { margin-left: 10px; }
        .footer-logos { display: flex; justify-content: center; align-items: center; margin-top: 40px; padding-top: 20px; gap: 60px; border-top: 1px solid #ccc; }
        .footer-logos img:first-child { height: 70px; }
        .footer-logos img:nth-child(2) { height: 90px; }
        .footer-logos img:nth-child(3) { height: 80px; }
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
    <div class="mb-3 text-start">
        <a href="geografico.php" class="btn btn-outline-success btn-sm ms-2">⬅️ Regresar</a>
    </div>

    <?php foreach ($cuadros as $index => $cuadro): ?>
        <h4 class="titulo-cuadro toggle-titulo" data-target="contenedor-<?php echo $index; ?>" style="text-align:center;">
                    <?php echo htmlspecialchars($cuadro['codigo_cuadro']) . ' - ' . htmlspecialchars($cuadro['cuadro_estadistico_titulo']); ?>
        </h4>

        <div id="contenedor-<?php echo $index; ?>" style="display:none;">
            <div class="descargas mb-2" style="display:flex; justify-content:center; gap:8px; flex-wrap:wrap; align-items:center;">
                <?php if (!empty($cuadro['pdf_file'])): ?>
                <a href="cuadro/uploads/<?= urlencode($tema) . '/pdf/' . htmlspecialchars($cuadro['pdf_file']) ?>" class="btn btn-outline-danger btn-sm" target="_blank">📄 PDF</a>
                <?php endif; ?>

                <?php if ($cuadro['permite_grafica'] == 1): ?>
                    <a href="generarGrafica.php?cuadro_id=<?= pathinfo($cuadro['cuadro_estadistico_id'], PATHINFO_FILENAME); ?>&tema=<?= urlencode($tema) ?>&archivo=<?= urlencode($archivo) ?>" 
                    class="btn btn-outline-success btn-sm">
                        📊 Gráfica
                    </a>
                <?php endif; ?>

                <?php if ($usuario === 'admin'): ?>
                    <a href="subir_pdf.php?id=<?= $cuadro['cuadro_estadistico_id'] ?>&tema=<?= $tema ?>" class="btn btn-outline-primary btn-sm">📤 Actualizar PDF</a>
                    <a href="subir_csv.php?id=<?= $cuadro['cuadro_estadistico_id'] ?>&tema=<?= $tema ?>" class="btn btn-outline-primary btn-sm">📥 Subir nuevo CSV</a>
                    <a href="editar_csv.php?archivo=<?= urlencode($cuadro['nombe_archivo_csv']); ?>&tema=<?= $tema ?>" class="btn btn-outline-dark btn-sm">✏️ Editar CSV</a>
                <?php endif; ?>

            </div>
            <div class="descargas mb-2" style="display:flex; justify-content:center; gap:8px; flex-wrap:wrap; align-items:center;">

                <?php mostrarCSV("cuadro/uploads/$tema/csv/" . $cuadro['nombe_archivo_csv']); ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="footer-logos mt-5">
    <img src="imagenes/sige2.png" alt="SIGEM Footer Logo">
    <img src="imagenes/logosfinales2.png" alt="IMIP Footer Logo">
    <img src="imagenes/logoadmin.png" alt="Ciudad Juárez Footer Logo">
</div>

<script>
    document.querySelectorAll('.toggle-titulo').forEach(function(titulo) {
        titulo.addEventListener('click', function() {
            const targetId = this.dataset.target;
            const contenedor = document.getElementById(targetId);
            if (contenedor.style.display === "block") {
                contenedor.style.display = "none";
            } else {
                document.querySelectorAll('[id^="contenedor-"]').forEach(function(div) {
                    div.style.display = "none";
                });
                contenedor.style.display = "block";
            }
        });
    });
</script>

</body>
</html>
