<?php
declare(strict_types=1);

/**
 * Instalador por consola: crea las tablas, carga el contenido inicial y crea
 * el primer administrador pidiendo la contraseña de forma interactiva.
 *
 *   php bin/install.php
 *
 * Es seguro ejecutarlo más de una vez: no borra datos existentes.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Solo por consola.');
}

require dirname(__DIR__) . '/app/bootstrap.php';

use App\Core\Auth;
use App\Core\Db;

function ask(string $q): string
{
    echo $q;
    return trim((string) fgets(STDIN));
}

echo "== Instalación de Veinticuatrosiete ==\n";

try {
    Db::pdo();
} catch (Throwable $e) {
    exit("No se pudo conectar a la base de datos. Revisa DB_* en .env\n" . $e->getMessage() . "\n");
}

foreach (['schema.sql', 'seed.sql'] as $f) {
    $sql = file_get_contents(APP_ROOT . '/database/' . $f);
    Db::pdo()->exec($sql);
    echo "OK  database/$f\n";
}

foreach (['public/uploads', 'storage/logs'] as $d) {
    if (!is_dir(APP_ROOT . "/$d")) {
        mkdir(APP_ROOT . "/$d", 0755, true);
    }
    echo (is_writable(APP_ROOT . "/$d") ? 'OK  ' : 'ATENCIÓN: sin permiso de escritura en ') . "$d\n";
}

$count = (int) Db::val('SELECT COUNT(*) FROM users');
if ($count > 0) {
    echo "Ya existe un administrador. Para crear otro o restablecer una clave: php bin/create-admin.php\n";
    exit(0);
}

echo "\nCrear el primer administrador\n";
$email = mb_strtolower(ask('Correo: '));
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    exit("Correo no válido.\n");
}
$name = ask('Nombre (opcional): ');
$pw = ask('Contraseña (mín. 10 caracteres, letras y números; se verá al escribirla): ');
$pw2 = ask('Repite la contraseña: ');
if ($pw !== $pw2) {
    exit("Las contraseñas no coinciden.\n");
}
if ($err = Auth::passwordError($pw)) {
    exit($err . "\n");
}
Db::insert(
    'INSERT INTO users (email, name, password_hash, created_at) VALUES (?, ?, ?, NOW())',
    [$email, $name, password_hash($pw, PASSWORD_DEFAULT)]
);
echo "\nListo. Ingresa en /admin con $email\n";
