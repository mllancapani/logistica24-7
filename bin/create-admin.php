<?php
declare(strict_types=1);

/**
 * Crea un administrador o restablece su contraseña.
 *   php bin/create-admin.php correo@dominio.cl
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Solo por consola.');
}
require dirname(__DIR__) . '/app/bootstrap.php';

use App\Core\Auth;
use App\Core\Db;

$email = mb_strtolower(trim($argv[1] ?? ''));
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    exit("Uso: php bin/create-admin.php correo@dominio.cl\n");
}
echo 'Contraseña (mín. 10 caracteres, letras y números; se verá al escribirla): ';
$pw = trim((string) fgets(STDIN));
if ($err = Auth::passwordError($pw)) {
    exit($err . "\n");
}
$hash = password_hash($pw, PASSWORD_DEFAULT);
if (Db::val('SELECT id FROM users WHERE email = ?', [$email])) {
    Db::exec('UPDATE users SET password_hash = ?, active = 1 WHERE email = ?', [$hash, $email]);
    echo "Contraseña actualizada para $email\n";
} else {
    Db::insert('INSERT INTO users (email, password_hash, created_at) VALUES (?, ?, NOW())', [$email, $hash]);
    echo "Administrador creado: $email\n";
}
