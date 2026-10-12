<?php
declare(strict_types=1);

use App\Controllers\Admin\AuthController;
use App\Controllers\Admin\ContentController;
use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\ItemsController;
use App\Controllers\ContactController;
use App\Controllers\PublicController;

/** @var App\Core\Router $router */

// ---- Sitio público ----
$router->get('/', [PublicController::class, 'home']);
$router->get('/nosotros', [PublicController::class, 'about']);
$router->get('/servicios', [PublicController::class, 'services']);
$router->get('/galeria', [PublicController::class, 'gallery']);
$router->get('/clientes', [PublicController::class, 'clients']);
$router->get('/ubicacion', [PublicController::class, 'location']);
$router->get('/contacto', [ContactController::class, 'show']);
$router->post('/contacto', [ContactController::class, 'submit']);
$router->get('/sitemap.xml', [PublicController::class, 'sitemap']);
$router->get('/robots.txt', [PublicController::class, 'robots']);

// ---- Panel de administración ----
$router->get('/admin/login', [AuthController::class, 'form']);
$router->post('/admin/login', [AuthController::class, 'login']);
$router->post('/admin/logout', [AuthController::class, 'logout']);
$router->get('/admin', [DashboardController::class, 'index']);

$router->get('/admin/textos', [ContentController::class, 'index']);
$router->get('/admin/textos/([a-z]+)', [ContentController::class, 'edit']);
$router->post('/admin/textos/([a-z]+)', [ContentController::class, 'save']);

$router->get('/admin/mensajes', [DashboardController::class, 'messages']);
$router->get('/admin/mensajes/(\d+)', [DashboardController::class, 'message']);
$router->post('/admin/mensajes/(\d+)/eliminar', [DashboardController::class, 'deleteMessage']);

$router->get('/admin/cuenta', [DashboardController::class, 'account']);
$router->post('/admin/cuenta', [DashboardController::class, 'changePassword']);

foreach (['servicios', 'galeria', 'clientes'] as $res) {
    $router->get("/admin/($res)", [ItemsController::class, 'index']);
    $router->get("/admin/($res)/nuevo", [ItemsController::class, 'create']);
    $router->post("/admin/($res)/nuevo", [ItemsController::class, 'store']);
    $router->get("/admin/($res)/(\\d+)/editar", [ItemsController::class, 'edit']);
    $router->post("/admin/($res)/(\\d+)/editar", [ItemsController::class, 'update']);
    $router->post("/admin/($res)/(\\d+)/eliminar", [ItemsController::class, 'destroy']);
    $router->post("/admin/($res)/(\\d+)/estado", [ItemsController::class, 'toggle']);
    $router->post("/admin/($res)/(\\d+)/(subir|bajar)", [ItemsController::class, 'move']);
}
