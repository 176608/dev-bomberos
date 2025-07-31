<?php 
// Función para mostrar una tabla HTML generada a partir de un archivo CSV
function mostrarCSV($ruta) {
    // Verifica si el archivo existe antes de intentar abrirlo
    if (!file_exists($ruta)) {
        return; // Si no existe, termina la función sin hacer nada
    }

    // Abre el archivo CSV en modo lectura
    if (($handle = fopen($ruta, "r")) !== false) {

        // Comienza la tabla HTML con una clase personalizada (siguiendo estilos del sistema)
        echo "<table class='table-sigem'>";

        // Intenta leer la primera línea del archivo como encabezados de la tabla
        if (($encabezados = fgetcsv($handle)) !== false) {
            echo "<thead><tr>";
            // Itera sobre cada campo de los encabezados
            foreach ($encabezados as $campo) {
                // Limpia el contenido y lo muestra como un <th> (encabezado de columna)
                echo "<th>" . htmlspecialchars(trim($campo)) . "</th>";
            }
            echo "</tr></thead>"; // Cierra la fila de encabezados
        }

        // Comienza el cuerpo de la tabla
        echo "<tbody>";

        // Lee línea por línea el resto del archivo CSV
        while (($fila = fgetcsv($handle)) !== false) {
            echo "<tr>"; // Nueva fila de la tabla
            foreach ($fila as $celda) {
                // Limpia cada celda y la muestra en un <td> (celda de tabla)
                echo "<td>" . htmlspecialchars(trim($celda)) . "</td>";
            }
            echo "</tr>"; // Cierra la fila
        }

        echo "</tbody></table>"; // Cierra el cuerpo y la tabla HTML

        // Cierra el archivo una vez terminado
        fclose($handle);
    }
}
?>
