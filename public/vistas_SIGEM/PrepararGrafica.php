<?php
// Incluye el controlador que prepara las variables necesarias para la configuración de la gráfica.
// Este archivo probablemente define $CuadroEstadistico, $EjeVerticalOpciones y $EjeHorizontalOpciones
include '../controllers/prepararGraficaController.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Configuración de Gráfica</title>
    <link rel="stylesheet" href="css/graficas.css">
    <link rel="stylesheet" href="js/plugins/multiselect/jquery.multiselect.css">
    <link rel="stylesheet" href="js/plugins/multiselect/jquery.multiselect.filter.css">
    <link rel="stylesheet" href="js/plugins/jquery-ui/jquery-ui.css">
    <script src="js/plugins/jquery.min.js"></script>
    <script src="js/plugins/jquery-ui/jquery-ui.js"></script>
    <script src="js/plugins/multiselect/src/jquery.multiselect.js"></script>
    <script src="js/plugins/multiselect/src/jquery.multiselect.filter.js"></script>
    <script>
    $(function(){
        $("#EjeVertical, #EjeHorizontal").multiselect({minWidth: 300});
    });
    </script>
</head>
<body>
<form action="generarGrafica.php" method="POST">
    <input type="hidden" name="CuadroEstadistico" value="<?= $CuadroEstadistico ?>">

    <fieldset>
        <legend>Tipo de Gráfica</legend>
        <?php
        $tipos = ['Barras', 'Columnas', 'Pie'];
        foreach ($tipos as $i => $tipo) {
            echo '<label><input type="radio" name="radioTipoGrafica" value="'.$tipo.'" '.($i==0?'checked':'').'> '.$tipo.'</label> ';
        }
        ?>
        <div>
            <label for="title">Título personalizado:</label>
            <input type="text" name="title" id="title" style="width:300px;">
        </div>
    </fieldset>

    <fieldset>
        <legend>Eje Vertical</legend>
        <select name="EjeVertical[]" id="EjeVertical" multiple="multiple"><?= $EjeVerticalOpciones ?></select>
    </fieldset>

    <fieldset>
        <legend>Eje Horizontal</legend>
        <select name="EjeHorizontal[]" id="EjeHorizontal" multiple="multiple"><?= $EjeHorizontalOpciones ?></select>
    </fieldset>
</form>
</body>
</html>
