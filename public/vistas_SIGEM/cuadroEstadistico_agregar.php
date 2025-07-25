<?php
include '../controllers/cuadroEstadistico_agregarController.php';

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Cuadro Estadistico</title>
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
    <h2 class="mb-4">Agregar Nuevo Cuadro Estadístico</h2>

    <form method="post" enctype="multipart/form-data">

        <div class="row mb-3">
            <div class="col-md-2">
                <label for="codigo_cuadro" class="form-label">Código</label>
                <input type="text" class="form-control" id="codigo_cuadro" name="codigo_cuadro" required>
            </div>


            <div class="col-md-9">
                    <label for="nombre_cuadro" class="form-label">Nombre del Cuadro Estadístico</label>
                    <input type="text" class="form-control" id="nombre_cuadro" name="nombre_cuadro" required>
            </div>

        </div>

        <div class="mb-3">
            <label for="tema" class="form-label">Tema Relacionado</label>
            <select class="form-select" id="tema" name="tema" required>
                <option value="">Selecciona un tema</option>
                <?php foreach ($temas as $tema): ?>
                    <option value="<?= htmlspecialchars($tema['nombre']) ?>"><?= htmlspecialchars($tema['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="subtema" class="form-label">Subtema Relacionado</label> 
            <select class="form-select" id="subtema" name="subtema" required disabled>
                <option value="">Selecciona un tema</option>
                <?php foreach ($subtemas as $sub): ?>
                    <option value="<?= htmlspecialchars($sub['nombre_subtema']) ?>" data-tema="<?= htmlspecialchars($sub['tema']) ?>">
                        <?= htmlspecialchars($sub['nombre_subtema']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
            <!-- PDF -->
<!--
    <div class="row mb-3">
        <div class="col-md-6">
            <label class="form-label">¿Deseas subir un archivo PDF?</label><br>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="subir_pdf" id="pdf_no" value="no" checked>
                <label class="form-check-label" for="pdf_no">No</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="subir_pdf" id="pdf_si" value="si">
                <label class="form-check-label" for="pdf_si">Sí</label>
            </div>
            <input type="file" name="archivo_pdf" id="archivo_pdf" accept="application/pdf" class="form-control mt-2" disabled>
        </div>


        <div class="col-md-6">
            <label class="form-label">¿Deseas subir un archivo CSV?</label><br>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="subir_csv" id="csv_no" value="no" checked>
                <label class="form-check-label" for="csv_no">No</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="subir_csv" id="csv_si" value="si">
                <label class="form-check-label" for="csv_si">Sí</label>
            </div>
            <input type="file" name="archivo_csv" id="archivo_csv" accept=".csv,text/csv" class="form-control mt-2" disabled>
        </div>

    </div>
    -->
        <button type="submit" class="btn btn-primary">Guardar</button>
        <a href="subtema.php" class="btn btn-secondary">Cancelar</a>
</div>

<!-- Pie de página -->
<div class="footer-logos mt-5">
    <img src="imagenes/sige2.png" alt="SIGEM Footer Logo">
    <img src="imagenes/logosfinales2.png" alt="IMIP Footer Logo">
    <img src="imagenes/logoadmin.png" alt="Ciudad Juárez Footer Logo">
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
/*

        // PDF
    const pdfRadios = document.getElementsByName('subir_pdf');
    const archivoPdf = document.getElementById('archivo_pdf');

    pdfRadios.forEach(radio => {
        radio.addEventListener('change', () => {
            archivoPdf.disabled = (radio.value !== 'si');
            if (archivoPdf.disabled) archivoPdf.value = '';
        });
    });

    // CSV
    const csvRadios = document.getElementsByName('subir_csv');
    const archivoCsv = document.getElementById('archivo_csv');

    csvRadios.forEach(radio => {
        radio.addEventListener('change', () => {
            archivoCsv.disabled = (radio.value !== 'si');
            if (archivoCsv.disabled) archivoCsv.value = '';
        });
    });*/
});

</script>

</body>
</html>
