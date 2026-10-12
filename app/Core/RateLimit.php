<?php
declare(strict_types=1);

namespace App\Core;

/** Límite de intentos por clave dentro de una ventana de tiempo. */
final class RateLimit
{
    /** Registra un intento y devuelve true si todavía está dentro del límite. */
    public static function hit(string $key, int $max, int $windowSec): bool
    {
        $key = substr(hash('sha256', $key), 0, 40);
        Db::exec(
            'INSERT INTO rate_limits (k, hits, window_start) VALUES (?, 1, NOW())
             ON DUPLICATE KEY UPDATE
               hits = IF(window_start < (NOW() - INTERVAL ? SECOND), 1, hits + 1),
               window_start = IF(window_start < (NOW() - INTERVAL ? SECOND), NOW(), window_start)',
            [$key, $windowSec, $windowSec]
        );
        return (int) Db::val('SELECT hits FROM rate_limits WHERE k = ?', [$key]) <= $max;
    }

    /** Consulta sin sumar un intento. */
    public static function exceeded(string $key, int $max, int $windowSec): bool
    {
        $key = substr(hash('sha256', $key), 0, 40);
        $n = Db::val(
            'SELECT hits FROM rate_limits WHERE k = ? AND window_start >= (NOW() - INTERVAL ? SECOND)',
            [$key, $windowSec]
        );
        return (int) $n >= $max;
    }

    public static function clear(string $key): void
    {
        Db::exec('DELETE FROM rate_limits WHERE k = ?', [substr(hash('sha256', $key), 0, 40)]);
    }

    /** Limpieza ocasional de registros viejos. */
    public static function gc(): void
    {
        if (random_int(1, 50) === 1) {
            Db::exec('DELETE FROM rate_limits WHERE window_start < (NOW() - INTERVAL 1 DAY)');
        }
    }
}
