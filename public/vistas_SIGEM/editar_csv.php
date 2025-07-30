<?php
// ⚠️ Vista privada de administración.
// Este archivo permite editar manualmente el contenido de un archivo CSV.
// Solo debe ser accesible para el usuario 'admin'.
// Ya depende de una sesión iniciada, pero aún puede mejorarse la validación de rol.
require_once '../controllers/editar_csvController.php';
?>
<?php require_once '../controllers/editar_csvController.php'; ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar CSV - SIGEM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #eeeeee; font-family: Arial, sans-serif; }
        .top-bar { background-color: #2a6e48; color: white; padding: 5px 15px; font-size: 14px; display: flex; justify-content: space-between; align-items: center; }
        .right-section { display: flex; align-items: center; gap: 15px; }
        .user-icon { width: 28px; height: 28px; background-color: white; color: #2a6e48; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        .header-logos { display: flex; align-items: center; gap: 20px; margin: 10px 0 10px 10px; }
        .header-logos img:first-child { height: 85px; }
        .header-logos img:last-child { height: 65px; }
        .footer-logos { display: flex; justify-content: center; align-items: center; margin-top: 40px; padding-top: 20px; gap: 60px; border-top: 1px solid #ccc; }
        .footer-logos img:first-child { height: 70px; }
        .footer-logos img:nth-child(2) { height: 90px; }
        .footer-logos img:nth-child(3) { height: 80px; }

        input[type='text'] {
            width: 100%;
            border: none;
            background-color: transparent;
        }
        input[type='text']:focus {
            outline: 1px solid #ccc;
            background-color: #f9f9f9;
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
        <div><strong><?php echo htmlspecialchars($_SESSION['usuario']); ?></strong></div>
        <div><a href="logout.php" class="btn btn-sm btn-light text-success">Cerrar sesión</a></div>
    </div>
</div>

<div class="header-logos">
    <img src="imagenes/sige1.png" alt="IMIP Logo">
    <img src="imagenes/sige2.png" alt="SIGEM Logo">
</div>

<?php include 'menuprincipal.php'; ?>

<div class="container mt-4">
    <h2 class="mb-4">✏️ Editar contenido del archivo CSV: <code><?= htmlspecialchars($archivo) ?></code></h2>

    <form method="POST">
        <input type="hidden" name="tema" value="<?= htmlspecialchars($tema) ?>">
        <input type="hidden" name="archivo" value="<?= htmlspecialchars($archivo) ?>">

        <div class="table-responsive">
            <table class="table table-bordered">
                <?php foreach ($filas as $i => $fila): ?>
                    <tr>
                        <?php foreach ($fila as $j => $valor): ?>
                            <td>
                                <input type="text" name="data[<?= $i ?>][<?= $j ?>]" value="<?= htmlspecialchars($valor) ?>">
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <button type="submit" class="btn btn-success">💾 Guardar cambios</button>
        <a href="<?= htmlspecialchars($tema) ?>.php" class="btn btn-secondary">↩️ Cancelar</a>
    </form>
</div>

<div class="footer-logos mt-5">
    <img src="imagenes/sige2.png" alt="SIGEM Footer Logo">
    <img src="imagenes/logosfinales2.png" alt="IMIP Footer Logo">
    <img src="imagenes/logoadmin.png" alt="Ciudad Juárez Footer Logo">
</div>

</body>
</html>
