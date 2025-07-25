<?php
/* <!-- Archivo Principal Routes - NO ELIMINAR COMENTARIO --> */

// Cargar rutas del sistema Bomberos
require __DIR__.'/Bomberos/web.php';

// Cargar rutas SIGEM originales (sistema PHP existente)
require __DIR__.'/SIGEM/web.php';

// Cargar rutas SIGEM Laravel (nuevo módulo Laravel)
require __DIR__.'/SIGEM/laravel.php';