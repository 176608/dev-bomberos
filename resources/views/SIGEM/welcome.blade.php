<?php 
include '../controllers/sesionController.php';

$mostrarLogout = isset($_SESSION['usuario']);
$usuario = $_SESSION['usuario'] ?? 'Público';

include '../models/conexion.php';

$query = "
    SELECT t.ce_tema_id, t.tema, s.ce_subtema_id, s.ce_subtema
    FROM consulta_express_tema t
    LEFT JOIN consulta_express_subtema s ON s.ce_tema_id = t.ce_tema_id
    ORDER BY t.tema ASC
";

$result = $conexion->query($query);
$temasConSubtemas = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $idTema = $row['ce_tema_id'];
        $temaNombre = $row['tema'];
        $subtema = null;
        if (!is_null($row['ce_subtema_id'])) {
            $subtema = [
                'ce_subtema_id' => $row['ce_subtema_id'],
                'ce_subtema' => $row['ce_subtema'],
            ];
        }

        $indexTema = null;
        foreach ($temasConSubtemas as $index => $tema) {
            if ($tema['id'] == $idTema) {
                $indexTema = $index;
                break;
            }
        }

        if ($indexTema === null) {
            $temasConSubtemas[] = [
                'id' => $idTema,
                'tema' => $temaNombre,
                'subtemas' => [],
            ];
            $indexTema = count($temasConSubtemas) - 1;
        }

        if ($subtema) {
            $temasConSubtemas[$indexTema]['subtemas'][] = $subtema;
        }
    }
}

$subtemaSeleccionado = $_GET['subtema_id'] ?? null;
$contenidoSubtema = null;

if ($subtemaSeleccionado) {
    $stmtContenido = $conexion->prepare("SELECT ce_contenido FROM consulta_express_contenido WHERE ce_subtema_id = ? LIMIT 1");
    $stmtContenido->bind_param("i", $subtemaSeleccionado);
    $stmtContenido->execute();
    $resultadoContenido = $stmtContenido->get_result();

    if ($rowContenido = $resultadoContenido->fetch_assoc()) {
        $contenidoSubtema = $rowContenido['ce_contenido'];
    }

    $stmtContenido->close();
}
?>

<!DOCTYPE html> 
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SIGEM</title>
    <link rel="icon" type="image/png" href="imagenes/imiplogo_200x200_trans.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #eeeeee;
        }
        .top-bar {
            background-color: #2a6e48;
            color: white;
            padding: 5px 15px;
            font-size: 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .top-bar .right-section {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .top-bar .user-icon {
            width: 28px;
            height: 28px;
            background-color: white;
            color: #2a6e48;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .header-logos {
            display: flex;
            align-items: center;
            gap: 20px;
            margin: 10px 0 10px 10px;
        }
        .header-logos img:first-child { height: 85px; }
        .header-logos img:last-child { height: 65px; }
        .main-card {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            margin: 0 auto;
            width: 90%;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .navbar-custom {
            background-color: transparent;
            margin-bottom: 20px;
        }
        .navbar-custom .nav-link {
            background-color: #2a6e48;
            color: white;
            margin: 0 10px;
            padding: 10px 25px;
            border-radius: 6px;
        }
        .navbar-custom .nav-link:hover { background-color: #1e4d36; }
        .module-icons {
            text-align: center;
            margin-top: 30px;
            margin-bottom: 30px;
        }
        .module-icons img { height: 100px; }
        .module-icons .description {
            font-size: 14px;
            margin-top: 10px;
        }
        .footer-logos {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 40px;
            padding-top: 20px;
            gap: 60px;
            border-top: 1px solid #ccc;
        }
        .footer-logos img:first-child { height: 70px; }
        .footer-logos img:nth-child(2) { height: 90px; }
        .footer-logos img:nth-child(3) { height: 80px; }
        #subtema-selector { display: block; }
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
        <div><strong><?= htmlspecialchars($usuario) ?></strong></div>

        <!-- Botón de Panel Admin para admin -->
        <?php if ($usuario === 'admin'): ?>
        <div>
            <a href="dashboard.php" style="background-color: white; color: #2a6e48; padding: 5px 10px; border: 1px solid #2a6e48; border-radius: 4px; font-size: 12px; text-decoration: none;">
                Panel Admin
            </a>
        </div>
        <?php endif; ?>

        <div>
            <?php if ($mostrarLogout): ?>
                <a href="logout.php" style="background-color: white; color: #2a6e48; padding: 5px 10px; text-decoration: none; border-radius: 4px; font-size: 12px;">Cerrar sesión</a>
            <?php else: ?>
                <a href="login.php" style="background-color: white; color: #2a6e48; padding: 5px 10px; text-decoration: none; border-radius: 4px; font-size: 12px;">Iniciar sesión</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="header-logos">
    <img src="imagenes/sige1.png" alt="IMIP Logo">
    <img src="imagenes/sige2.png" alt="SIGEM Logo">
</div>

<?php include 'menuprincipal.php'; ?>

<div class="main-card">

    <div class="row module-icons">
        <div class="col-md-6">
            <img src="imagenes/iconoesta2.png" alt="Estadística">
            <div class="description">Consultas de información estadística relevante y precisa en cuadros estadísticos, obtenidos de diversas fuentes Municipales, Estatales y Federales.</div>
        </div>
        <div class="col-md-6">
            <img src="imagenes/iconoesta3.png" alt="Cartografía">
            <div class="description">En este apartado podrás encontrar mapas temáticos interactivos del Municipio de Juárez.</div>
        </div>
    </div>

    <div class="mt-4">
        <div class="row mb-3">
            <div class="col-md-6">
                <select id="tema-selector" class="form-select">
                    <option value="">Selecciona un tema</option>
                </select>
            </div>
            <div class="col-md-6">
                <select id="subtema-selector" class="form-select">
                    <option value="">Seleccione un tema primero</option>
                </select>
            </div>
        </div>

        <?php if ($contenidoSubtema): ?>
            <div class="alert alert-info mt-4">
                <?= $contenidoSubtema ?>
            </div>
        <?php endif; ?>

        <div id="contenido-dinamico" class="mt-4"></div>
    </div>
</div>

<div class="footer-logos mt-5">
    <img src="imagenes/sige2.png" alt="SIGEM Footer Logo">
    <img src="imagenes/logosfinales2.png" alt="IMIP Footer Logo">
    <img src="imagenes/logoadmin.png" alt="Ciudad Juárez Footer Logo">
</div>

<script>
    const temasConSubtemas = <?= json_encode($temasConSubtemas) ?>;
    const temaSelector = document.getElementById('tema-selector');
    const subtemaSelector = document.getElementById('subtema-selector');
    const contenidoDinamico = document.getElementById('contenido-dinamico');

    function cargarTemas() {
        temaSelector.innerHTML = '<option value="">Selecciona un tema</option>';
        for (const i in temasConSubtemas) {
            const option = document.createElement('option');
            option.value = i;
            option.textContent = temasConSubtemas[i].tema;
            temaSelector.appendChild(option);
        }
        subtemaSelector.innerHTML = '<option value="">Seleccione un tema primero</option>';
        subtemaSelector.disabled = true;
    }

    function cargarSubtemas(temaId) {
        subtemaSelector.innerHTML = '';
        if (!temaId || !temasConSubtemas[temaId]) {
            subtemaSelector.innerHTML = '<option value="">Seleccione un tema primero</option>';
            subtemaSelector.disabled = true;
            return;
        }

        const subtemas = temasConSubtemas[temaId].subtemas;
        if (subtemas.length === 0) {
            subtemaSelector.innerHTML = '<option value="">No hay subtemas disponibles</option>';
            subtemaSelector.disabled = true;
            return;
        }

        subtemaSelector.innerHTML = '<option value="">Seleccione un subtema</option>';
        subtemas.forEach(subtema => {
            const option = document.createElement('option');
            option.value = subtema.ce_subtema_id;
            option.textContent = subtema.ce_subtema;
            subtemaSelector.appendChild(option);
        });

        subtemaSelector.disabled = false;
    }

    function actualizarContenido() {
        const temaId = temaSelector.value;
        const subtemaId = subtemaSelector.value;

        if (!temaId || !subtemaId) {
            contenidoDinamico.innerHTML = '';
            return;
        }

        fetch(`/contenido-tema?subtema_id=${encodeURIComponent(subtemaId)}`)
            .then(response => response.text())
            .then(data => {
                contenidoDinamico.innerHTML = data;
            })
            .catch(error => {
                contenidoDinamico.innerHTML = `<div class="alert alert-danger">Error al cargar contenido.</div>`;
                console.error('Error al obtener datos:', error);
            });
    }

    temaSelector.addEventListener('change', () => {
        cargarSubtemas(temaSelector.value);
        contenidoDinamico.innerHTML = '';
    });

    subtemaSelector.addEventListener('change', actualizarContenido);

    cargarTemas();
</script>

</body>
</html>
