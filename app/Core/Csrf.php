<?php
declare(strict_types=1);

namespace App\Core;

final class Csrf
{
    public static function token(): string
    {
        Session::start();
        if (empty($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf'];
    }

    public static function valid(?string $t): bool
    {
        Session::start();
        return $t !== null && !empty($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $t);
    }

    /** Aborta con 419 si el token no coincide. */
    public static function check(): void
    {
        if (!self::valid($_POST['_csrf'] ?? null)) {
            Http::abort(419, 'Tu sesión expiró. Vuelve atrás, recarga la página e inténtalo de nuevo.');
        }
    }
}
