<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Http;
use App\Core\Images;
use App\Core\Session;
use App\Core\Settings;

final class ContentController
{
    private static function groups(): array
    {
        return require APP_ROOT . '/app/Content.php';
    }

    public static function index(): void
    {
        Auth::require();
        DashboardController::render('admin/content_index', ['section' => 'textos', 'groups' => self::groups()]);
    }

    public static function edit(string $gid): void
    {
        Auth::require();
        $groups = self::groups();
        if (!isset($groups[$gid])) {
            Http::abort(404);
        }
        DashboardController::render('admin/content_edit', [
            'section' => 'textos', 'gid' => $gid, 'group' => $groups[$gid], 'groups' => $groups, 'errors' => [],
        ]);
    }

    public static function save(string $gid): void
    {
        Auth::require();
        Csrf::check();
        $groups = self::groups();
        if (!isset($groups[$gid])) {
            Http::abort(404);
        }
        $errors = [];
        $values = [];
        $confirm = (array) ($_POST['ok'] ?? []);

        foreach ($groups[$gid]['fields'] as $f) {
            $k = $f['key'];
            $type = $f['type'];
            if ($type === 'image') {
                continue;
            }
            $raw = $_POST['f'][$k] ?? '';
            $v = is_string($raw) ? trim($raw) : '';
            if ($type === 'check') {
                $v = isset($_POST['f'][$k]) ? '1' : '0';
            }
            $max = (int) ($f['max'] ?? 2000);
            if (mb_strlen($v) > $max) {
                $errors[$k] = "Máximo $max caracteres.";
            } elseif ($type === 'url' && $v !== '' && !preg_match('#^https?://[^\s]+$#i', $v)) {
                $errors[$k] = 'Escribe la dirección completa, empezando con https://';
            } elseif ($type === 'email' && $v !== '' && !filter_var($v, FILTER_VALIDATE_EMAIL)) {
                $errors[$k] = 'Escribe un correo válido.';
            } elseif ($type === 'emails' && $v !== '') {
                foreach (preg_split('/[,;]\s*/', $v) ?: [] as $a) {
                    if (!filter_var(trim($a), FILTER_VALIDATE_EMAIL)) {
                        $errors[$k] = 'Revisa los correos: ' . trim($a);
                        break;
                    }
                }
            } elseif ($type === 'tel' && $v !== '' && !preg_match('/^[0-9+\-()\s.]{6,30}$/', $v)) {
                $errors[$k] = 'Solo números, espacios, + y guiones.';
            }
            $values[$k] = $type === 'textarea' ? str_replace("\r\n", "\n", $v) : $v;
        }

        // Imágenes
        $imgSaves = [];
        foreach ($groups[$gid]['fields'] as $f) {
            if ($f['type'] !== 'image') {
                continue;
            }
            $k = $f['key'];
            $file = $_FILES['img_' . $k] ?? null;
            if (!empty($_POST['rm_' . $k])) {
                $imgSaves[$k] = ['remove' => true];
            }
            if ($file && ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
                try {
                    $imgSaves[$k] = ['id' => Images::store($file, 'photo')];
                } catch (\RuntimeException $e) {
                    $errors[$k] = $e->getMessage();
                }
            }
            if (!empty($f['alt'])) {
                $alt = trim((string) ($_POST['f'][$f['alt']] ?? ''));
                $values[$f['alt']] = mb_substr($alt, 0, 200);
            }
        }

        if ($errors) {
            // Si hubo errores se descartan las imágenes ya procesadas.
            foreach ($imgSaves as $s) {
                if (isset($s['id'])) {
                    Images::delete($s['id']);
                }
            }
            http_response_code(422);
            DashboardController::render('admin/content_edit', [
                'section' => 'textos', 'gid' => $gid, 'group' => $groups[$gid], 'groups' => $groups,
                'errors' => $errors, 'posted' => $values,
            ]);
            return;
        }

        foreach ($values as $k => $v) {
            Settings::save($k, $v, isset($confirm[$k]));
        }
        foreach ($imgSaves as $k => $s) {
            $old = (int) Settings::get($k) ?: null;
            if (isset($s['id'])) {
                Settings::set($k, (string) $s['id']);
                Images::delete($old);
            } elseif (!empty($s['remove'])) {
                Settings::set($k, '');
                Images::delete($old);
            }
        }
        Session::flash('ok', 'Cambios guardados.');
        Http::redirect('/admin/textos/' . $gid);
    }
}
