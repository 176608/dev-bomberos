<?php
// 🔐 logout.php: destruye la sesión actual y redirige a una vista pública
?>
<?php
session_start();
session_unset();
session_destroy();
header('Location: index.php'); // O a / si estás usando Laravel
exit();
?>
