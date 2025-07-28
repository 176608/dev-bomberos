<?php
// Este script gestiona una vista pública del sistema SIGEM.
// Obtiene los subtemas correspondientes a un tema sin requerir autenticación.

require_once '../models/subtemasModel.php';

$tema_id = 3; // Tema Geográfico o el que corresponda

$subtemas = obtenerSubtemasPorTema($tema_id);
