<?php 
function mostrarCSV($ruta) {
    if (!file_exists($ruta)) {
        return;
    }

    if (($handle = fopen($ruta, "r")) !== false) {
        echo "<table class='table-sigem'>";
        
        // Leer encabezados
        if (($encabezados = fgetcsv($handle)) !== false) {
            echo "<thead><tr>";
            foreach ($encabezados as $campo) {
                echo "<th>" . htmlspecialchars(trim($campo)) . "</th>";
            }
            echo "</tr></thead>";
        }

        // Leer contenido
        echo "<tbody>";
        while (($fila = fgetcsv($handle)) !== false) {
            echo "<tr>";
            foreach ($fila as $celda) {
                echo "<td>" . htmlspecialchars(trim($celda)) . "</td>";
            }
            echo "</tr>";
        }
        echo "</tbody></table>";

        fclose($handle);
    }
}
?>