<?php
include '../controllers/subir_csvController.php';
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
        .main-card { background-color: white; border-radius: 8px; padding: 30px; margin: 0 auto; width: 80%; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .footer-logos { display: flex; justify-content: center; align-items: center; margin-top: 40px; padding-top: 20px; gap: 60px; border-top: 1px solid #ccc; }
        .footer-logos img:first-child { height: 70px; }
        .footer-logos img:nth-child(2) { height: 90px; }
        .footer-logos img:nth-child(3) { height: 80px; }
    </style>
</head>
<body>

<!-- Barra superior -->
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
        <div><a href="logout.php" class="btn btn-sm btn-light text-success">Cerrar sesión</a></div>
    </div>
</div>

<!-- Logos superiores -->
<div class="header-logos">
    <img src="imagenes/sige1.png" alt="IMIP Logo">
    <img src="imagenes/sige2.png" alt="SIGEM Logo">
</div>

<!-- Menú principal -->
<?php include 'menuprincipal.php'; ?>

<!-- Contenido principal -->
<div class="main-card">
    <h3 class="text-center text-success mb-4">Actualizar CSV para el cuadro ID <?php echo htmlspecialchars($cuadro_id); ?></h3>

    <form id="formCSV" action="guardar_csv.php" method="post" enctype="multipart/form-data" class="text-center">
        <input type="hidden" name="cuadro_id" value="<?php echo htmlspecialchars($cuadro_id); ?>">
        <input type="hidden" name="tema" value="<?php echo htmlspecialchars($tema); ?>">

        <div class="mb-3 w-50 mx-auto">
            <input type="file" name="nuevo_csv" accept=".csv" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Subir CSV</button>
        <a href="javascript:history.back()" class="btn btn-secondary ms-2">Cancelar</a>
        <button class="btn btn-outline-success ms-2" onclick="history.back()">← Regresar</button>
    </form>
</div>

<!-- Logos inferiores -->
<div class="footer-logos mt-5">
    <img src="imagenes/sige2.png" alt="SIGEM Footer Logo">
    <img src="imagenes/logosfinales2.png" alt="IMIP Footer Logo">
    <img src="imagenes/logoadmin.png" alt="Ciudad Juárez Footer Logo">
</div>

<!-- Confirmación JavaScript -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const formulario = document.getElementById('formCSV');
    if (formulario) {
        formulario.addEventListener('submit', function (e) {
            const confirmado = confirm("⚠️ Esta acción reemplazará el archivo CSV actual.\n¿Deseas continuar?");
            if (!confirmado) e.preventDefault();
        });
    }
});
</script>

</body>
</html>
