<?php
declare(strict_types=1);

/**
 * Respaldo: vuelca la base de datos a SQL y comprime las imágenes subidas.
 *   php bin/backup.php            -> crea storage/backups/AAAA-MM-DD_HHMM/ (conserva los últimos 14)
 * Programa este comando con una tarea cron diaria y descarga los respaldos fuera del servidor.
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Solo por consola.');
}
require dirname(__DIR__) . '/app/bootstrap.php';

use App\Core\Db;

$root = APP_ROOT . '/storage/backups';
$dir = $root . '/' . date('Y-m-d_Hi');
if (!is_dir($dir) && !mkdir($dir, 0750, true)) {
    exit("No se pudo crear $dir\n");
}
@file_put_contents($root . '/.htaccess', "Require all denied\n");

// 1) Base de datos
$pdo = Db::pdo();
$out = fopen($dir . '/base_de_datos.sql', 'w');
fwrite($out, "-- Respaldo " . date('c') . "\nSET NAMES utf8mb4;\nSET FOREIGN_KEY_CHECKS=0;\n\n");
foreach ($pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN) as $t) {
    if ($t === 'rate_limits') {
        continue;
    }
    $create = $pdo->query("SHOW CREATE TABLE `$t`")->fetch(PDO::FETCH_NUM)[1];
    fwrite($out, "DROP TABLE IF EXISTS `$t`;\n$create;\n\n");
    $st = $pdo->query("SELECT * FROM `$t`");
    while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
        $vals = array_map(static fn($v) => $v === null ? 'NULL' : $pdo->quote((string) $v), $row);
        fwrite($out, "INSERT INTO `$t` VALUES (" . implode(',', $vals) . ");\n");
    }
    fwrite($out, "\n");
}
fwrite($out, "SET FOREIGN_KEY_CHECKS=1;\n");
fclose($out);
echo "OK  base_de_datos.sql\n";

// 2) Imágenes
if (class_exists(ZipArchive::class)) {
    $zip = new ZipArchive();
    $zip->open($dir . '/imagenes.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);
    foreach (glob(PUBLIC_ROOT . '/uploads/*') ?: [] as $f) {
        if (is_file($f) && basename($f)[0] !== '.') {
            $zip->addFile($f, basename($f));
        }
    }
    $zip->close();
    echo "OK  imagenes.zip\n";
} else {
    echo "AVISO: la extensión zip no está disponible; copia la carpeta public/uploads manualmente.\n";
}

// 3) Rotación: se conservan los 14 más recientes
$all = glob($root . '/*', GLOB_ONLYDIR) ?: [];
sort($all);
foreach (array_slice($all, 0, max(0, count($all) - 14)) as $old) {
    foreach (glob($old . '/*') ?: [] as $f) {
        @unlink($f);
    }
    @rmdir($old);
}
echo "Respaldo en $dir\n";
