<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Db;
use App\Core\Http;
use App\Core\Images;
use App\Core\Session;
use App\Core\View;

final class DashboardController
{
    public static function render(string $view, array $data = []): void
    {
        $user = Auth::require();
        $data += [
            'user' => $user,
            'flash' => Session::pullFlash(),
            'unread' => (int) Db::val('SELECT COUNT(*) FROM contact_messages WHERE is_read = 0'),
            'section' => $data['section'] ?? '',
        ];
        View::render($view, $data, 'admin/layout');
    }

    public static function index(): void
    {
        Auth::require();
        $groups = require APP_ROOT . '/app/Content.php';
        $labels = [];
        foreach ($groups as $gid => $g) {
            foreach ($g['fields'] as $f) {
                $labels[$f['key']] = [$g['title'], $gid, $f['label']];
            }
        }
        $pending = [];
        foreach (\App\Core\Settings::all() as $k => $row) {
            if ($row['d'] === 1 && trim($row['v']) !== '' && isset($labels[$k]) && $labels[$k][1] !== 'seo') {
                $pending[] = $labels[$k];
            }
        }
        $pendingServices = (int) Db::val('SELECT COUNT(*) FROM services WHERE is_draft = 1');

        $checks = [];
        if (trim((string) env('MAIL_HOST', '')) === '') {
            $checks[] = 'El envío de correo no está configurado (MAIL_HOST en el archivo .env). Las consultas se guardan igual en Mensajes.';
        }
        if (!is_writable(Images::dir())) {
            $checks[] = 'La carpeta de imágenes no tiene permiso de escritura. No se podrán subir fotos.';
        }
        if (env('SHOW_PROVISIONAL', 'true') === 'true' && ($pending || $pendingServices)) {
            $checks[] = 'El sitio muestra marcas de “texto provisional”. Cuando el contenido esté confirmado, pon SHOW_PROVISIONAL=false en .env.';
        }
        self::render('admin/dashboard', [
            'section' => 'panel',
            'stats' => [
                'Servicios activos' => (int) Db::val('SELECT COUNT(*) FROM services WHERE active = 1'),
                'Imágenes en galería' => (int) Db::val('SELECT COUNT(*) FROM gallery_items WHERE active = 1'),
                'Clientes publicados' => (int) Db::val('SELECT COUNT(*) FROM clients WHERE active = 1'),
                'Mensajes sin leer' => (int) Db::val('SELECT COUNT(*) FROM contact_messages WHERE is_read = 0'),
            ],
            'pending' => $pending,
            'pendingServices' => $pendingServices,
            'checks' => $checks,
        ]);
    }

    public static function messages(): void
    {
        Auth::require();
        $rows = Db::all('SELECT id, name, company, email, topic, mail_ok, is_read, created_at FROM contact_messages ORDER BY created_at DESC, id DESC LIMIT 200');
        self::render('admin/messages', ['section' => 'mensajes', 'rows' => $rows]);
    }

    public static function message(string $id): void
    {
        Auth::require();
        $m = Db::one('SELECT * FROM contact_messages WHERE id = ?', [(int) $id]);
        if (!$m) {
            Http::abort(404);
        }
        Db::exec('UPDATE contact_messages SET is_read = 1 WHERE id = ?', [$m['id']]);
        self::render('admin/message', ['section' => 'mensajes', 'm' => $m]);
    }

    public static function deleteMessage(string $id): void
    {
        Auth::require();
        Csrf::check();
        Db::exec('DELETE FROM contact_messages WHERE id = ?', [(int) $id]);
        Session::flash('ok', 'Mensaje eliminado.');
        Http::redirect('/admin/mensajes');
    }

    public static function account(): void
    {
        Auth::require();
        self::render('admin/account', ['section' => 'cuenta', 'error' => null]);
    }

    public static function changePassword(): void
    {
        $user = Auth::require();
        Csrf::check();
        $cur = (string) ($_POST['current'] ?? '');
        $new = (string) ($_POST['new'] ?? '');
        $rep = (string) ($_POST['repeat'] ?? '');
        $row = Db::one('SELECT password_hash FROM users WHERE id = ?', [$user['id']]);
        $err = null;
        if (!$row || !password_verify($cur, $row['password_hash'])) {
            $err = 'La contraseña actual no es correcta.';
        } elseif ($new !== $rep) {
            $err = 'Las contraseñas nuevas no coinciden.';
        } elseif ($e = Auth::passwordError($new)) {
            $err = $e;
        } elseif (password_verify($new, $row['password_hash'])) {
            $err = 'La nueva contraseña debe ser distinta de la actual.';
        }
        if ($err) {
            http_response_code(422);
            self::render('admin/account', ['section' => 'cuenta', 'error' => $err]);
            return;
        }
        Db::exec('UPDATE users SET password_hash = ? WHERE id = ?', [password_hash($new, PASSWORD_DEFAULT), $user['id']]);
        session_regenerate_id(true);
        Session::flash('ok', 'Contraseña actualizada.');
        Http::redirect('/admin/cuenta');
    }
}
