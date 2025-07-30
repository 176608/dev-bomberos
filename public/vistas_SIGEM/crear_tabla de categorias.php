
<?php
// ⚙️ Script privado para uso administrativo (NO es vista pública).
// Este archivo crea una tabla en la base de datos 'sigem_replica'.
// Debe ejecutarse una sola vez o controlarse con lógica adicional para evitar errores por tabla ya existente.
?>
<?php
$mysqli = new mysqli("localhost", "root", "", "sigem_replica");

if ($mysqli->connect_error) {
    die("Error de conexión: " . $mysqli->connect_error);
}

$sql = "CREATE TABLE categorias_cuadros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL
)";

if ($mysqli->query($sql)) {
    echo "Tabla creada exitosamente!";
} else {
    echo "Error al crear tabla: " . $mysqli->error;
}

$mysqli->close();
?>