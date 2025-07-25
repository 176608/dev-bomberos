
<?php  

//Forman parte de la lógica de control del flujo de usuario (autenticado o no).

    include 'sesionController.php';
    include '../models/catalogoModel.php';


$cuadros = obtenerCuadros();

?>  