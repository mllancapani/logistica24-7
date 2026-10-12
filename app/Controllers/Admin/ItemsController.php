<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Db;
use App\Core\Http;
use App\Core\Images;
use App\Core\Session;

/** CRUD genérico para servicios, galería y clientes. */
final class ItemsController
{
    private const CFG = [
        'servicios' => [
            'table' => 'services', 'title' => 'Servicios', 'one' => 'servicio', 'new' => 'Nuevo servicio',
            'list' => 'title', 'draft' => true,
            'fields' => [
                ['title', 'Título', 'text', true, 140, 'Nombre corto del servicio.'],
                ['description', 'Descripción', 'textarea', false, 1500, 'Dos o tres frases que expliquen el servicio.'],
                ['show_cta', 'Mostrar el botón “Solicitar información”', 'check', false, 0, ''],
            ],
            'image' => ['kind' => 'photo', 'required' => false, 'alt' => true, 'label' => 'Imagen representativa', 'help' => 'Recomendado: horizontal (16:10). Se optimiza automáticamente.'],
        ],
        'galeria' => [
            'table' => 'gallery_items', 'title' => 'Galería', 'one' => 'imagen', 'new' => 'Agregar imagen',
            'list' => 'title', 'draft' => false,
            'fields' => [['title', 'Título o descripción (opcional)', 'text', false, 160, 'Se muestra sobre la foto y al ampliarla.']],
            'image' => ['kind' => 'photo', 'required' => true, 'alt' => true, 'label' => 'Fotografía', 'help' => 'Se recorta visualmente en formato 4:3 en la grilla; al ampliarla se ve completa.'],
        ],
        'clientes' => [
            'table' => 'clients', 'title' => 'Clientes', 'one' => 'cliente', 'new' => 'Nuevo cliente',
            'list' => 'name', 'draft' => false,
            'fields' => [
                ['name', 'Nombre del cliente', 'text', true, 160, ''],
                ['url', 'Sitio web (opcional)', 'url', false, 255, 'Dirección completa, empezando con https://'],
            ],
            'image' => ['kind' => 'logo', 'required' => true, 'alt' => false, 'label' => 'Logotipo', 'help' => 'Solo sube logotipos de clientes que autorizaron su publicación. PNG con fondo transparente funciona mejor.'],
        ],
    ];

    private static function cfg(string $res): array
    {
        return self::CFG[$res] ?? Http::abort(404);
    }

    private static function find(array $c, string $id): array
    {
        $row = Db::one("SELECT t.*, m.file, m.thumb FROM {$c['table']} t LEFT JOIN media m ON m.id = t.media_id WHERE t.id = ?", [(int) $id]);
        return $row ?? Http::abort(404);
    }

    public static function index(string $res): void
    {
        Auth::require();
        $c = self::cfg($res);
        $rows = Db::all("SELECT t.*, m.thumb FROM {$c['table']} t LEFT JOIN media m ON m.id = t.media_id ORDER BY t.sort_order, t.id");
        DashboardController::render('admin/items_list', ['section' => $res, 'res' => $res, 'c' => $c, 'rows' => $rows]);
    }

    public static function create(string $res): void
    {
        Auth::require();
        $c = self::cfg($res);
        DashboardController::render('admin/items_form', [
            'section' => $res, 'res' => $res, 'c' => $c, 'row' => ['active' => 1, 'show_cta' => 1], 'errors' => [], 'isNew' => true,
        ]);
    }

    public static function edit(string $res, string $id): void
    {
        Auth::require();
        $c = self::cfg($res);
        DashboardController::render('admin/items_form', [
            'section' => $res, 'res' => $res, 'c' => $c, 'row' => self::find($c, $id), 'errors' => [], 'isNew' => false,
        ]);
    }

    /** @return array{0: array, 1: array} valores y errores */
    private static function collect(array $c, bool $isNew): array
    {
        $vals = [];
        $err = [];
        foreach ($c['fields'] as [$col, $label, $type, $req, $max]) {
            if ($type === 'check') {
                $vals[$col] = isset($_POST[$col]) ? 1 : 0;
                continue;
            }
            $v = trim((string) ($_POST[$col] ?? ''));
            if ($type === 'textarea') {
                $v = str_replace("\r\n", "\n", $v);
            }
            if ($req && $v === '') {
                $err[$col] = 'Este campo es obligatorio.';
            } elseif ($max && mb_strlen($v) > $max) {
                $err[$col] = "Máximo $max caracteres.";
            } elseif ($type === 'url' && $v !== '' && !preg_match('#^https?://[^\s]+$#i', $v)) {
                $err[$col] = 'Escribe la dirección completa, empezando con https://';
            }
            $vals[$col] = $v;
        }
        if (!empty($c['image']['alt'])) {
            $alt = trim((string) ($_POST['image_alt'] ?? ''));
            if (mb_strlen($alt) > 200) {
                $err['image_alt'] = 'Máximo 200 caracteres.';
            }
            $vals['image_alt'] = $alt;
        }
        $vals['active'] = isset($_POST['active']) ? 1 : 0;
        return [$vals, $err];
    }

    private static function redisplay(string $res, array $c, array $row, array $errors, bool $isNew): void
    {
        http_response_code(422);
        DashboardController::render('admin/items_form', [
            'section' => $res, 'res' => $res, 'c' => $c, 'row' => $row, 'errors' => $errors, 'isNew' => $isNew,
        ]);
    }

    public static function store(string $res): void
    {
        Auth::require();
        Csrf::check();
        $c = self::cfg($res);
        [$vals, $err] = self::collect($c, true);
        $hasFile = ($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
        if ($c['image']['required'] && !$hasFile) {
            $err['image'] = 'Selecciona una imagen.';
        }
        $mediaId = null;
        if (!$err && $hasFile) {
            try {
                $mediaId = Images::store($_FILES['image'], $c['image']['kind']);
            } catch (\RuntimeException $e) {
                $err['image'] = $e->getMessage();
            }
        }
        if ($err) {
            self::redisplay($res, $c, $vals, $err, true);
            return;
        }
        $vals['media_id'] = $mediaId;
        $vals['sort_order'] = (int) Db::val("SELECT COALESCE(MAX(sort_order), 0) + 1 FROM {$c['table']}");
        $vals['created_at'] = date('Y-m-d H:i:s');
        $cols = array_keys($vals);
        Db::insert(
            "INSERT INTO {$c['table']} (" . implode(',', $cols) . ') VALUES (' . implode(',', array_fill(0, count($cols), '?')) . ')',
            array_values($vals)
        );
        Session::flash('ok', ucfirst($c['one']) . ' creado.');
        Http::redirect("/admin/$res");
    }

    public static function update(string $res, string $id): void
    {
        Auth::require();
        Csrf::check();
        $c = self::cfg($res);
        $row = self::find($c, $id);
        [$vals, $err] = self::collect($c, false);
        $hasFile = ($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
        $remove = !$c['image']['required'] && !empty($_POST['remove_image']);
        $newMedia = null;
        if (!$err && $hasFile) {
            try {
                $newMedia = Images::store($_FILES['image'], $c['image']['kind']);
            } catch (\RuntimeException $e) {
                $err['image'] = $e->getMessage();
            }
        }
        if ($err) {
            self::redisplay($res, $c, $vals + $row, $err, false);
            return;
        }
        $oldMedia = $row['media_id'] ? (int) $row['media_id'] : null;
        if ($newMedia) {
            $vals['media_id'] = $newMedia;
        } elseif ($remove) {
            $vals['media_id'] = null;
        }
        if ($c['draft']) {
            $vals['is_draft'] = 0;
        }
        $set = implode(', ', array_map(static fn($k) => "$k = ?", array_keys($vals)));
        Db::exec("UPDATE {$c['table']} SET $set WHERE id = ?", [...array_values($vals), $row['id']]);
        if (($newMedia || $remove) && $oldMedia) {
            Images::delete($oldMedia);
        }
        Session::flash('ok', 'Cambios guardados.');
        Http::redirect("/admin/$res");
    }

    public static function destroy(string $res, string $id): void
    {
        Auth::require();
        Csrf::check();
        $c = self::cfg($res);
        $row = self::find($c, $id);
        Db::exec("DELETE FROM {$c['table']} WHERE id = ?", [$row['id']]);
        if ($row['media_id']) {
            Images::delete((int) $row['media_id']);
        }
        Session::flash('ok', ucfirst($c['one']) . ' eliminado.');
        Http::redirect("/admin/$res");
    }

    public static function toggle(string $res, string $id): void
    {
        Auth::require();
        Csrf::check();
        $c = self::cfg($res);
        $row = self::find($c, $id);
        Db::exec("UPDATE {$c['table']} SET active = 1 - active WHERE id = ?", [$row['id']]);
        Session::flash('ok', $row['active'] ? ucfirst($c['one']) . ' despublicado.' : ucfirst($c['one']) . ' publicado.');
        Http::redirect("/admin/$res");
    }

    public static function move(string $res, string $id, string $dir): void
    {
        Auth::require();
        Csrf::check();
        $c = self::cfg($res);
        $ids = array_map('intval', array_column(Db::all("SELECT id FROM {$c['table']} ORDER BY sort_order, id"), 'id'));
        $i = array_search((int) $id, $ids, true);
        if ($i !== false) {
            $j = $dir === 'subir' ? $i - 1 : $i + 1;
            if (isset($ids[$j])) {
                [$ids[$i], $ids[$j]] = [$ids[$j], $ids[$i]];
                foreach ($ids as $pos => $rid) {
                    Db::exec("UPDATE {$c['table']} SET sort_order = ? WHERE id = ?", [$pos + 1, $rid]);
                }
            }
        }
        Http::redirect("/admin/$res#f$id");
    }
}
