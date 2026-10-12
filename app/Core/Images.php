<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Subida segura de imágenes: valida el contenido real, vuelve a codificar con GD
 * (elimina metadatos y cualquier carga maliciosa), genera miniatura y guarda
 * con nombre aleatorio. Nunca conserva el archivo original.
 */
final class Images
{
    private const MIMES = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

    public static function dir(): string
    {
        return PUBLIC_ROOT . '/uploads';
    }

    /**
     * @param string $kind 'photo' (máx. 1600 px) o 'logo' (máx. 600 px, conserva transparencia)
     * @return int id de la fila en media
     * @throws \RuntimeException con mensaje apto para el usuario
     */
    public static function store(array $file, string $kind = 'photo'): int
    {
        $err = $file['error'] ?? UPLOAD_ERR_NO_FILE;
        if ($err !== UPLOAD_ERR_OK) {
            throw new \RuntimeException(match ($err) {
                UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'La imagen supera el tamaño máximo permitido.',
                UPLOAD_ERR_NO_FILE => 'Selecciona una imagen.',
                default => 'No se pudo subir la imagen. Inténtalo de nuevo.',
            });
        }
        $tmp = $file['tmp_name'] ?? '';
        if (!is_uploaded_file($tmp) && env('APP_ENV') !== 'testing') {
            throw new \RuntimeException('Archivo no válido.');
        }
        $maxBytes = (int) env('MAX_UPLOAD_MB', '5') * 1024 * 1024;
        if (filesize($tmp) > $maxBytes) {
            throw new \RuntimeException('La imagen pesa más de ' . (int) env('MAX_UPLOAD_MB', '5') . ' MB.');
        }

        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($tmp);
        if (!isset(self::MIMES[$mime])) {
            throw new \RuntimeException('Formato no permitido. Usa JPG, PNG o WebP.');
        }
        $info = @getimagesize($tmp);
        if (!$info || $info[0] < 80 || $info[1] < 80) {
            throw new \RuntimeException('La imagen es demasiado pequeña (mínimo 80 × 80 px).');
        }
        if ($info[0] > 8000 || $info[1] > 8000 || $info[0] * $info[1] > 40_000_000) {
            throw new \RuntimeException('Las dimensiones de la imagen son demasiado grandes.');
        }

        $src = match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($tmp),
            'image/png' => @imagecreatefrompng($tmp),
            default => @imagecreatefromwebp($tmp),
        };
        if (!$src) {
            throw new \RuntimeException('No se pudo leer la imagen. Prueba con otro archivo.');
        }
        if ($mime === 'image/jpeg' && function_exists('exif_read_data')) {
            $src = self::fixOrientation($src, $tmp);
        }

        $max = $kind === 'logo' ? 600 : 1600;
        $hasAlpha = $mime !== 'image/jpeg';
        $webp = function_exists('imagewebp');
        $ext = $webp ? 'webp' : ($hasAlpha ? 'png' : 'jpg');

        $base = bin2hex(random_bytes(12));
        $file1 = "$base.$ext";
        $file2 = "$base-t.$ext";
        $dir = self::dir();
        if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
            throw new \RuntimeException('No se pudo crear la carpeta de imágenes.');
        }

        [$main, $w, $h] = self::resize($src, $max);
        self::save($main, "$dir/$file1", $ext);
        [$thumb] = self::resize($src, $kind === 'logo' ? 300 : 640);
        self::save($thumb, "$dir/$file2", $ext);
        imagedestroy($src);
        imagedestroy($main);
        imagedestroy($thumb);
        @chmod("$dir/$file1", 0644);
        @chmod("$dir/$file2", 0644);

        return Db::insert(
            'INSERT INTO media (file, thumb, width, height, bytes, created_at) VALUES (?,?,?,?,?,NOW())',
            [$file1, $file2, $w, $h, (int) filesize("$dir/$file1")]
        );
    }

    /** Borra el archivo y su fila. */
    public static function delete(?int $id): void
    {
        if (!$id) {
            return;
        }
        $m = Db::one('SELECT file, thumb FROM media WHERE id = ?', [$id]);
        if ($m) {
            foreach (['file', 'thumb'] as $c) {
                $p = self::dir() . '/' . basename($m[$c]);
                if (is_file($p)) {
                    @unlink($p);
                }
            }
            Db::exec('DELETE FROM media WHERE id = ?', [$id]);
        }
    }

    public static function get(?int $id): ?array
    {
        return $id ? Db::one('SELECT * FROM media WHERE id = ?', [$id]) : null;
    }

    private static function resize(\GdImage $src, int $max): array
    {
        $w = imagesx($src);
        $h = imagesy($src);
        $r = min(1, $max / max($w, $h));
        $nw = max(1, (int) round($w * $r));
        $nh = max(1, (int) round($h * $r));
        $dst = imagecreatetruecolor($nw, $nh);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        imagefill($dst, 0, 0, imagecolorallocatealpha($dst, 255, 255, 255, 127));
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);
        return [$dst, $nw, $nh];
    }

    private static function save(\GdImage $img, string $path, string $ext): void
    {
        $ok = match ($ext) {
            'webp' => imagewebp($img, $path, 82),
            'png' => imagepng($img, $path, 7),
            default => self::saveJpeg($img, $path),
        };
        if (!$ok) {
            throw new \RuntimeException('No se pudo guardar la imagen optimizada.');
        }
    }

    private static function saveJpeg(\GdImage $img, string $path): bool
    {
        $w = imagesx($img);
        $h = imagesy($img);
        $flat = imagecreatetruecolor($w, $h);
        imagefill($flat, 0, 0, imagecolorallocate($flat, 255, 255, 255));
        imagecopy($flat, $img, 0, 0, 0, 0, $w, $h);
        $ok = imagejpeg($flat, $path, 84);
        imagedestroy($flat);
        return $ok;
    }

    private static function fixOrientation(\GdImage $img, string $path): \GdImage
    {
        $exif = @exif_read_data($path);
        $o = $exif['Orientation'] ?? 1;
        $rot = match ($o) { 3 => 180, 6 => -90, 8 => 90, default => 0 };
        if ($rot) {
            $r = imagerotate($img, $rot, 0);
            if ($r) {
                imagedestroy($img);
                return $r;
            }
        }
        return $img;
    }
}
