<?php
// 🔐 Vista de tipo ADMIN
// Muestra un formulario de parámetros para graficar datos de cuadros estadísticos, con edición dinámica y acceso a archivos.
// La vista permite graficar, editar el título, elegir columnas y tipo de gráfica.
// Aunque no modifica directamente archivos, su uso está pensado exclusivamente para usuarios con sesión activa y permisos especiales.
?>
<?php

include '../controllers/generarGraficaController.php';

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($titulo) ?> - SIGEM</title>
    <script src="https://www.gstatic.com/charts/loader.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #eeeeee; font-family: Arial, sans-serif; }
        .top-bar {
            background-color: #2a6e48; color: white; padding: 5px 15px; font-size: 14px;
            display: flex; justify-content: space-between; align-items: center;
        }
        .top-bar .right-section {
            display: flex; align-items: center; gap: 15px;
        }
        .top-bar .user-icon {
            width: 28px; height: 28px; background-color: white; color: #2a6e48;
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
        }
        .header-logos {
            display: flex; align-items: center; padding: 10px 30px; gap: 20px;
        }
        .header-logos img:first-child { height: 85px; }
        .header-logos img:last-child { height: 65px; margin-left: 0; margin-right: auto; }
        .footer-logos {
            display: flex; justify-content: center; align-items: center; gap: 50px; padding: 20px 0; border-top: 1px solid #ccc;
        }
        .footer-logos img { height: 70px; }
        .main-card {
            background-color: white; border-radius: 8px; padding: 30px; margin: 20px auto; width: 90%; max-width: 850px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 { color: #2a6e48; text-align: center; }
        .mensaje { color: red; text-align: center; font-weight: bold; margin-top: 15px; }
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
        <strong><?= htmlspecialchars($usuario) ?></strong>
        <a href="<?= $usuario !== 'Público' ? 'logout.php' : 'login.php' ?>" class="btn btn-sm btn-light text-success">
            <?= $usuario !== 'Público' ? 'Cerrar sesión' : 'Iniciar sesión' ?>
        </a>
    </div>
</div>

<div class="header-logos">
    <img src="/imagenes/sige1.png" alt="Logo IMIP">
    <img src="/imagenes/sige2.png" alt="Logo SIGEM">
</div>

<?php include 'menuprincipal.php'; ?>

<div class="main-card">


<div class="mb-3 text-start">
    <?php
        if (!empty($archivo) && $archivo !== 'NULO') {
            // Prioridad 1: Si hay archivo, se usa directamente
            $url_regreso = htmlspecialchars($archivo) . '.php';
            $texto_boton = ucfirst($archivo);
        } else {
            // Prioridad 2: Usar tema y subtema si hay
            $url_regreso = htmlspecialchars($tema) . '.php';
            if (!empty($subtema_id) && intval($subtema_id) > 0) {
                $url_regreso .= '?subtema_id=' . urlencode($subtema_id);
            }
            $texto_boton = ucfirst($tema);
        }
    ?>
    <a href="<?= $url_regreso ?>" class="btn btn-outline-success">
        ← Regresar a <?= $texto_boton ?>
    </a>
</div>


    <h2><?= htmlspecialchars($titulo) ?></h2>

    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <?php
        $eje_x = $_POST['eje_x'] ?? '';
        $eje_y = $_POST['eje_y'] ?? [];
        $tipo = $_POST['tipo'] ?? 'ColumnChart';
        $titulo = $_POST['titulo'] ?? 'Gráfico';
        $mensaje = '';

        if ($tipo === 'PieChart' && count($eje_y) > 1) {
            $mensaje = "⚠️ Solo se puede seleccionar una columna para gráficos de pastel. Se usará: <strong>" . htmlspecialchars($eje_y[0]) . "</strong>";
            $eje_y = array_slice($eje_y, 0, 1);
        }
        ?>

        <?php if ($mensaje): ?>
            <div class="mensaje"><?= $mensaje ?></div>
        <?php endif; ?>

        <div id="grafica" style="width:100%; height:500px;"></div>

        <script>
        google.charts.load('current', { packages: ['corechart'] });
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            const data = google.visualization.arrayToDataTable([
                [<?= "'" . addslashes($eje_x) . "'" . implode('', array_map(fn($y) => ", '".addslashes($y)."'", $eje_y)) ?>],
                <?php
                foreach ($data as $fila) {
                    $index_x = array_search($eje_x, $headers);
                    if ($index_x === false) continue;
                    $valor_x = $fila[$index_x] ?? '';
                    $valores = ["'" . addslashes($valor_x) . "'"];
                    foreach ($eje_y as $y) {
                        $index_y = array_search($y, $headers);
                        if ($index_y === false) {
                            $valores[] = "0";
                            continue;
                        }
                        $valor_y = str_replace([',', '%', ' '], '', $fila[$index_y] ?? '');
                        $valores[] = is_numeric($valor_y) ? floatval($valor_y) : 0;
                    }
                    echo "[" . implode(",", $valores) . "],\n";
                }
                ?>
            ]);

            const options = {
                title: '<?= addslashes($titulo) ?>',
                height: 500,
                legend: { position: 'top' },
                hAxis: { title: '<?= addslashes($eje_x) ?>' },
                vAxis: { title: '<?= addslashes(implode(", ", $eje_y)) ?>' }
            };

            const chart = new google.visualization.<?= $tipo ?>(document.getElementById('grafica'));
            chart.draw(data, options);
        }
        </script>

        <div class="text-center mt-4">
            <a href="generarGrafica.php?cuadro_id=<?= urlencode($cuadro_id) ?>&tema=<?= urlencode($tema) ?>" class="btn btn-secondary">🔁 Cambiar parámetros</a>
        </div>

    <?php else: ?>
        
        <form method="POST" class="formulario">
            
            <input type="hidden" name="cuadro" value="<?= htmlspecialchars($cuadro_id) ?>">
            <label for="titulo">Título de la gráfica:</label>
            <input type="text" name="titulo" class="form-control" placeholder="Ejemplo: Población por localidad">

            <label for="tipo">Tipo de gráfica:</label>
            <select name="tipo" class="form-select">
                <option value="ColumnChart">📊 Barras verticales</option>
                <option value="BarChart">📉 Barras horizontales</option>
                <option value="LineChart">📈 Línea</option>
                <option value="PieChart">🟠 Pastel</option>
            </select>

            <label for="eje_x">Columna eje X:</label>
            <select name="eje_x" class="form-select" required>
                <?php foreach ($headers as $col): ?>
                    <option value="<?= htmlspecialchars($col) ?>"><?= htmlspecialchars($col) ?></option>
                <?php endforeach; ?>
            </select>

            <label for="eje_y">Columnas eje Y:</label>
            <select name="eje_y[]" class="form-select" size="5" multiple required>
                <?php foreach ($columnas_numericas as $col): ?>
                    <option value="<?= htmlspecialchars($col) ?>"><?= htmlspecialchars($col) ?></option>
                <?php endforeach; ?>
            </select>

            <div class="text-center mt-3">
                <button type="submit" class="btn btn-success">📈 Graficar</button>

            </div>
        </form>
    <?php endif; ?>
</div>

<div class="footer-logos">
    <img src="imagenes/sige2.png" alt="Logo SIGEM Footer">
    <img src="imagenes/logosfinales2.png" alt="Logo IMIP Footer">
    <img src="imagenes/logoadmin.png" alt="Logo Ciudad Juárez Footer">
</div>


</body>
</html>
