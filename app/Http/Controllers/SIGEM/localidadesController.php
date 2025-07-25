<?php  
require_once 'sesionController.php';
require_once '../models/localidadesModel.php';
include '../models/conexion.php';
include '../public/mostrar_csv.php';

$tema = 'geografico';

$tituloPagina = "Geográfico";

$cuadros = obtenerCuadrosConCSV($conexion);

$temas = obtenerTemas($conexion);

$archivo='localidades';

