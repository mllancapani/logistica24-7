<?php
declare(strict_types=1);

namespace App\Core;

final class Auth
{
    private const IDLE = 7200; // 2 horas sin actividad

    public static function user(): ?array
    {
        Session::start();
        $id = $_SESSION['uid'] ?? null;
        if (!$id) {
            return null;
        }
        if (time() - ($_SESSION['last'] ?? 0) > self::IDLE) {
            self::logout();
            return null;
        }
        $_SESSION['last'] = time();
        static $cache = [];
        return $cache[$id] ??= Db::one('SELECT id, email, name FROM users WHERE id = ? AND active = 1', [$id]);
    }

    /** Exige sesión de administrador o redirige al login. */
    public static function require(): array
    {
        $u = self::user();
        if (!$u) {
            if (Http::wantsJson()) {
                Http::json(['ok' => false, 'error' => 'Sesión expirada'], 401);
            }
            Http::redirect('/admin/login');
        }
        header('Cache-Control: no-store');
        return $u;
    }

    /** @return string|null mensaje de error, o null si el acceso fue correcto */
    public static function attempt(string $email, string $password): ?string
    {
        $ip = client_ip();
        $email = mb_strtolower(trim($email));
        if (RateLimit::exceeded("login:ip:$ip", 15, 900) || RateLimit::exceeded("login:em:$email", 6, 900)) {
            return 'Demasiados intentos. Espera 15 minutos y vuelve a intentarlo.';
        }
        $u = Db::one('SELECT * FROM users WHERE email = ? AND active = 1', [$email]);
        // Se verifica siempre un hash para no revelar si el correo existe por el tiempo de respuesta.
        $hash = $u['password_hash'] ?? '$2y$12$BSX/CUiisXNMQpR.qURLk.KE4t6JDqjevL/a.KmW8iBy.oO83olcO';
        $ok = password_verify($password, $hash) && $u;
        if (!$ok) {
            RateLimit::hit("login:ip:$ip", 15, 900);
            RateLimit::hit("login:em:$email", 6, 900);
            return 'Correo o contraseña incorrectos.';
        }
        RateLimit::clear("login:em:$email");
        if (password_needs_rehash($u['password_hash'], PASSWORD_DEFAULT)) {
            Db::exec('UPDATE users SET password_hash = ? WHERE id = ?', [password_hash($password, PASSWORD_DEFAULT), $u['id']]);
        }
        Session::start();
        session_regenerate_id(true);
        $_SESSION['uid'] = (int) $u['id'];
        $_SESSION['last'] = time();
        Db::exec('UPDATE users SET last_login = NOW() WHERE id = ?', [$u['id']]);
        return null;
    }

    public static function logout(): void
    {
        Session::start();
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 3600, $p['path'], $p['domain'] ?? '', $p['secure'], $p['httponly']);
        }
        session_destroy();
    }

    /** Valida requisitos de contraseña. @return string|null error */
    public static function passwordError(string $pw): ?string
    {
        if (mb_strlen($pw) < 10) {
            return 'La contraseña debe tener al menos 10 caracteres.';
        }
        if (!preg_match('/[A-Za-z]/', $pw) || !preg_match('/\d/', $pw)) {
            return 'La contraseña debe combinar letras y números.';
        }
        return null;
    }
}
