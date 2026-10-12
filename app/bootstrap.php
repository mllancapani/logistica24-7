<?php
declare(strict_types=1);

/**
 * Arranque de la aplicación: autoload, entorno, errores, sesión y helpers.
 */

define('APP_ROOT', dirname(__DIR__));
if (!defined('PUBLIC_ROOT')) {
    define('PUBLIC_ROOT', APP_ROOT . '/public');
}

spl_autoload_register(static function (string $class): void {
    if (strncmp($class, 'App\\', 4) !== 0) {
        return;
    }
    $file = APP_ROOT . '/app/' . str_replace('\\', '/', substr($class, 4)) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

require APP_ROOT . '/app/helpers.php';

App\Core\Env::load(APP_ROOT . '/.env');

date_default_timezone_set(env('APP_TZ', 'America/Punta_Arenas'));
mb_internal_encoding('UTF-8');

$debug = env('APP_ENV', 'production') === 'local';
ini_set('display_errors', $debug ? '1' : '0');
ini_set('log_errors', '1');
ini_set('error_log', APP_ROOT . '/storage/logs/php-error.log');
error_reporting(E_ALL);

set_exception_handler(static function (Throwable $e) use ($debug): void {
    error_log((string) $e);
    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: text/html; charset=utf-8');
    }
    if ($debug) {
        echo '<pre>' . e((string) $e) . '</pre>';
        return;
    }
    $page = APP_ROOT . '/app/Views/error.php';
    if (is_file($page)) {
        $code = 500;
        include $page;
    } else {
        echo 'Error interno.';
    }
});
