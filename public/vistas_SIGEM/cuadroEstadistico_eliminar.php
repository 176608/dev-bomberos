<?php
// ⚠️ Script privado de administración.
// Este archivo elimina un cuadro estadístico por su ID, por lo que debe estar protegido.
// Solo debe ser accesible para el usuario 'admin'.
// Actualmente no tiene ninguna validación de sesión o de permisos, lo cual representa un riesgo.
?>

<?php
include '../models/cuadroEstadistico_eliminarModel.php';

$id = $_GET['id'] ?? null;

if ($id) {
    eliminarSubtemaPorId($id);
}

header("Location: cuadroEstadistico.php");
exit;
