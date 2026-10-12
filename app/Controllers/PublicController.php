<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Db;
use App\Core\Http;
use App\Core\View;

final class PublicController
{
    public static function serviceRows(): array
    {
        return Db::all(
            'SELECT s.*, m.file, m.thumb, m.width, m.height FROM services s
             LEFT JOIN media m ON m.id = s.media_id
             WHERE s.active = 1 ORDER BY s.sort_order, s.id'
        );
    }

    public static function page(string $key, string $view, array $data = [], ?string $ogImage = null): void
    {
        $data += [
            'page' => $key,
            'seoTitle' => setting("seo_{$key}_t", 'Veinticuatrosiete Logística SpA'),
            'seoDesc' => setting("seo_{$key}_d"),
            'ogImage' => $ogImage,
        ];
        View::render("public/$view", $data);
    }

    /** Datos de cada sección, compartidos por la página de inicio y las páginas individuales. */
    public static function aboutData(): array
    {
        $values = [];
        foreach (preg_split('/\R/', setting('nos_valores')) ?: [] as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            $parts = explode(':', $line, 2);
            $values[] = ['n' => trim($parts[0]), 'd' => trim($parts[1] ?? '')];
        }
        return ['values' => $values, 'img' => \App\Core\Images::get((int) setting('nos_img') ?: null)];
    }

    public static function galleryData(): array
    {
        return ['galleryItems' => Db::all(
            'SELECT g.*, m.file, m.thumb, m.width, m.height FROM gallery_items g
             JOIN media m ON m.id = g.media_id WHERE g.active = 1 ORDER BY g.sort_order, g.id'
        )];
    }

    public static function clientsData(): array
    {
        return ['clientItems' => Db::all(
            'SELECT c.*, m.file, m.thumb FROM clients c
             LEFT JOIN media m ON m.id = c.media_id WHERE c.active = 1 AND c.media_id IS NOT NULL
             ORDER BY c.sort_order, c.id'
        )];
    }

    public static function locationData(): array
    {
        $parts = array_filter([setting('ubi_dir'), setting('ubi_com'), setting('ubi_ciu'), setting('ubi_reg'), 'Chile'], 'strlen');
        $addr = implode(', ', array_unique($parts));
        $hasAddr = trim(setting('ubi_dir')) !== '';
        return [
            'addr' => $addr,
            'hasAddr' => $hasAddr,
            'mapsLink' => trim(setting('ubi_map')) !== ''
                ? setting('ubi_map')
                : ($hasAddr ? 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($addr) : ''),
            'dirLink' => $hasAddr ? 'https://www.google.com/maps/dir/?api=1&destination=' . rawurlencode($addr) : '',
            'mapEmbed' => $hasAddr ? 'https://www.google.com/maps?q=' . rawurlencode($addr) . '&output=embed' : '',
        ];
    }

    public static function home(): void
    {
        \App\Core\Session::start();
        $_SESSION['form_t'] = time();
        $hero = \App\Core\Images::get((int) setting('ini_hero_img') ?: null);
        $service = trim((string) ($_GET['servicio'] ?? ''));
        $old = [
            'topic' => isset($_GET['servicio']) || ($_GET['asunto'] ?? '') === 'cotizacion' ? ContactController::TOPICS[0] : '',
            'message' => $service !== '' ? 'Quiero solicitar información sobre: ' . mb_substr($service, 0, 140) . '.' : '',
        ];
        self::page('inicio', 'home', [
            'services' => self::serviceRows(),
            'hero' => $hero,
            'old' => $old,
            'errors' => [],
            'showGallery' => setting('sec_gallery_on', '1') === '1',
            'showClients' => setting('sec_clients_on', '1') === '1',
        ] + self::aboutData() + self::galleryData() + self::clientsData() + self::locationData(),
            $hero ? upload_url($hero['file']) : null);
    }

    public static function about(): void
    {
        self::page('nosotros', 'about', self::aboutData());
    }

    public static function services(): void
    {
        self::page('servicios', 'services', ['services' => self::serviceRows()]);
    }

    public static function gallery(): void
    {
        if (setting('sec_gallery_on', '1') !== '1') {
            Http::abort(404);
        }
        self::page('galeria', 'gallery', self::galleryData());
    }

    public static function clients(): void
    {
        if (setting('sec_clients_on', '1') !== '1') {
            Http::abort(404);
        }
        self::page('clientes', 'clients', self::clientsData());
    }

    public static function location(): void
    {
        self::page('ubicacion', 'location', self::locationData());
    }

    public static function sitemap(): void
    {
        $pages = ['/' => 1.0, '/nosotros' => 0.8, '/servicios' => 0.9, '/ubicacion' => 0.6, '/contacto' => 0.9];
        if (setting('sec_gallery_on', '1') === '1') {
            $pages['/galeria'] = 0.5;
        }
        if (setting('sec_clients_on', '1') === '1') {
            $pages['/clientes'] = 0.5;
        }
        header('Content-Type: application/xml; charset=utf-8');
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($pages as $p => $prio) {
            echo '  <url><loc>' . e(url($p)) . '</loc><priority>' . number_format($prio, 1) . "</priority></url>\n";
        }
        echo '</urlset>';
    }

    public static function robots(): void
    {
        header('Content-Type: text/plain; charset=utf-8');
        echo "User-agent: *\nDisallow: /admin\nDisallow: /uploads/tmp/\n\nSitemap: " . url('/sitemap.xml') . "\n";
    }
}
