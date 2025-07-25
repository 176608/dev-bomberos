<?php
require_once '../models/subtemasModel.php';

$tema_id = 3; // Tema Geográfico o el que corresponda

$subtemas = obtenerSubtemasPorTema($tema_id);
