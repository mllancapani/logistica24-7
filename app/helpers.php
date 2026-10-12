<?php
declare(strict_types=1);

use App\Core\Env;
use App\Core\Settings;

function env(string $key, mixed $default = null): mixed
{
    return Env::get($key, $default);
}

/** Escapa texto para HTML. */
function e(mixed $v): string
{
    return htmlspecialchars((string) $v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** URL absoluta de la ruta indicada, sin barra final salvo la raíz. */
function url(string $path = '/'): string
{
    $base = rtrim((string) env('APP_URL', ''), '/');
    return $base . '/' . ltrim($path, '/');
}

/** Ruta relativa al sitio (sin dominio) para enlaces internos. */
function href(string $path = '/'): string
{
    return '/' . ltrim($path, '/');
}

/** Recurso estático con versión por fecha de modificación (cache busting). */
function asset(string $path): string
{
    $file = PUBLIC_ROOT . '/' . ltrim($path, '/');
    $v = is_file($file) ? (string) filemtime($file) : '1';
    return '/' . ltrim($path, '/') . '?v=' . $v;
}

/** Ruta pública de un archivo subido. */
function upload_url(?string $file): string
{
    return $file ? '/uploads/' . rawurlencode($file) : '';
}

/** Valor de configuración editable. */
function setting(string $key, string $default = ''): string
{
    return Settings::get($key, $default);
}

/** Atributo que marca contenido provisional (solo si está activado). */
function prov(string $key): string
{
    return Settings::isDraft($key) && env('SHOW_PROVISIONAL', 'true') === 'true' ? ' data-prov' : '';
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(App\Core\Csrf::token()) . '">';
}

function nl2p(string $text): string
{
    $parts = preg_split('/\R{2,}/', trim($text)) ?: [];
    $out = '';
    foreach ($parts as $p) {
        if (trim($p) !== '') {
            $out .= '<p>' . nl2br(e(trim($p))) . '</p>';
        }
    }
    return $out;
}

/** Solo dígitos para enlaces tel: y wa.me. */
function digits(string $s): string
{
    return preg_replace('/\D+/', '', $s) ?? '';
}

function client_ip(): string
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    if (env('TRUST_PROXY', 'false') === 'true' && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = trim(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0]);
    }
    return filter_var($ip, FILTER_VALIDATE_IP) ?: '0.0.0.0';
}

function is_https(): bool
{
    return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (env('TRUST_PROXY', 'false') === 'true' && ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
}
