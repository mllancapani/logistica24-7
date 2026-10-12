<?php
declare(strict_types=1);

// Servidor de desarrollo (php -S): deja que sirva los archivos estáticos existentes.
if (PHP_SAPI === 'cli-server') {
    $f = __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if (is_file($f) && basename($f) !== 'index.php') {
        return false;
    }
}

// Carpeta pública (este directorio). Si el hosting separa public_html del código,
// cambia la ruta de $app por la carpeta donde subiste "app/".
define('PUBLIC_ROOT', __DIR__);
$app = dirname(__DIR__);
require $app . '/app/bootstrap.php';

use App\Core\Http;
use App\Core\Router;

Http::securityHeaders();

$router = new Router();
require APP_ROOT . '/app/routes.php';

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $path);
