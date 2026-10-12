<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Csrf;
use App\Core\Db;
use App\Core\Http;
use App\Core\Mailer;
use App\Core\RateLimit;
use App\Core\Session;
use App\Core\View;

final class ContactController
{
    public const TOPICS = ['Solicitar cotización', 'Información sobre servicios', 'Otra consulta'];

    public static function show(): void
    {
        Session::start();
        $_SESSION['form_t'] = time();
        $service = trim((string) ($_GET['servicio'] ?? ''));
        $old = [
            'topic' => isset($_GET['servicio']) || ($_GET['asunto'] ?? '') === 'cotizacion' ? self::TOPICS[0] : '',
            'message' => $service !== '' ? 'Quiero solicitar información sobre: ' . mb_substr($service, 0, 140) . '.' : '',
        ];
        PublicController::page('contacto', 'contact', ['old' => $old, 'errors' => []]);
    }

    public static function submit(): void
    {
        Session::start();
        Csrf::check();
        $json = Http::wantsJson();

        // Anti-spam: campo trampa y tiempo mínimo de llenado.
        $started = (int) ($_SESSION['form_t'] ?? 0);
        if (($_POST['website'] ?? '') !== '' || $started === 0 || time() - $started < 3) {
            // Se responde como si hubiera funcionado para no dar pistas a los bots.
            self::done($json, true);
        }

        $ip = client_ip();
        RateLimit::gc();
        if (!RateLimit::hit("contact:$ip", 5, 3600)) {
            self::fail($json, ['_' => 'Has enviado varios mensajes seguidos. Intenta nuevamente en una hora o escríbenos por WhatsApp o teléfono.'], [], 429);
        }

        $clean = static fn(string $k, int $max): string => mb_substr(trim(preg_replace('/[\x00-\x1F\x7F]+/u', ' ', (string) ($_POST[$k] ?? '')) ?? ''), 0, $max);
        $d = [
            'name' => $clean('name', 120),
            'company' => $clean('company', 140),
            'email' => $clean('email', 190),
            'phone' => $clean('phone', 40),
            'topic' => $clean('topic', 80),
        ];
        $d['message'] = mb_substr(trim(str_replace("\0", '', (string) ($_POST['message'] ?? ''))), 0, 3000);

        $err = [];
        if (mb_strlen($d['name']) < 2) {
            $err['name'] = 'Escribe tu nombre.';
        }
        if (!filter_var($d['email'], FILTER_VALIDATE_EMAIL)) {
            $err['email'] = 'Escribe un correo válido, por ejemplo nombre@empresa.cl.';
        }
        if ($d['phone'] !== '' && !preg_match('/^[0-9+\-()\s.]{7,40}$/', $d['phone'])) {
            $err['phone'] = 'El teléfono solo puede tener números, espacios, + y guiones.';
        }
        if (!in_array($d['topic'], self::TOPICS, true)) {
            $err['topic'] = 'Selecciona el motivo de tu consulta.';
        }
        if (mb_strlen($d['message']) < 10) {
            $err['message'] = 'Cuéntanos un poco más (mínimo 10 caracteres).';
        }
        if ($err) {
            self::fail($json, $err, $d);
        }

        $id = Db::insert(
            'INSERT INTO contact_messages (name, company, email, phone, topic, message, ip, created_at)
             VALUES (?,?,?,?,?,?,?,NOW())',
            [$d['name'], $d['company'], $d['email'], $d['phone'], $d['topic'], $d['message'], $ip]
        );

        $body = "Nueva consulta desde el sitio web\n\n"
            . "Nombre: {$d['name']}\nEmpresa: " . ($d['company'] ?: '-') . "\nCorreo: {$d['email']}\n"
            . 'Teléfono: ' . ($d['phone'] ?: '-') . "\nMotivo: {$d['topic']}\n\nMensaje:\n{$d['message']}\n\n"
            . '-- Recibido ' . date('d-m-Y H:i') . " (consulta #$id)\n";
        $error = Mailer::send('[Sitio web] ' . $d['topic'] . ' - ' . $d['name'], $body, $d['email'], $d['name']);
        if ($error !== null) {
            error_log("Contacto #$id: correo no enviado: $error");
        }
        Db::exec(
            'UPDATE contact_messages SET mail_ok = ?, mail_error = ? WHERE id = ?',
            [$error === null ? 1 : 0, $error === null ? null : mb_substr($error, 0, 250), $id]
        );
        $_SESSION['form_t'] = time();
        self::done($json, false);
    }

    private static function done(bool $json, bool $silent): never
    {
        if ($json) {
            Http::json(['ok' => true]);
        }
        Session::flash('ok', 'Gracias. Recibimos tu mensaje y te responderemos a la brevedad.');
        Http::redirect('/contacto#enviado');
    }

    private static function fail(bool $json, array $errors, array $old, int $code = 422): never
    {
        if ($json) {
            Http::json(['ok' => false, 'errors' => $errors], $code);
        }
        http_response_code($code);
        PublicController::page('contacto', 'contact', ['old' => $old, 'errors' => $errors]);
        exit;
    }
}
