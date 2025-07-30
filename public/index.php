<?php
// =============================================================
// ARCHIVO DE ARRANQUE DEL FRAMEWORK LARAVEL (NO ES VISTA PÚBLICA)
// =============================================================
// Este archivo es el punto de entrada de todas las solicitudes HTTP.
// Inicializa Laravel cargando el autoloader de Composer, verifica
// si la aplicación está en modo mantenimiento y arranca el núcleo
// de la aplicación para procesar la petición actual.

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));


// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
