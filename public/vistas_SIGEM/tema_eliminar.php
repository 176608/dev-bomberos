<?php
// =============================================
// Vista de ADMINISTRADOR - Eliminar Tema
// =============================================
// Este script elimina un tema de la base de datos
// utilizando el ID recibido por la URL (método GET).
// ⚠️ Requiere protección para que solo el usuario 'admin' pueda ejecutarlo.


include '../models/tema_eliminarModel.php';



$id = $_GET['id'] ?? null;

if ($id) {
eliminarTemaPorId($id);
}

header("Location: tema.php");
exit();
?>