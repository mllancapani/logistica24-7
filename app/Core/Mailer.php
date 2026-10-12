<?php
declare(strict_types=1);

namespace App\Core;

require_once APP_ROOT . '/app/Vendor/PHPMailer/Exception.php';
require_once APP_ROOT . '/app/Vendor/PHPMailer/PHPMailer.php';
require_once APP_ROOT . '/app/Vendor/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\Exception as MailException;
use PHPMailer\PHPMailer\PHPMailer;

/** Envío de correo por SMTP corporativo (configurado en .env). */
final class Mailer
{
    public static function recipient(): string
    {
        $to = trim(Settings::get('con_dest'));
        return $to !== '' ? $to : (string) env('MAIL_TO', '');
    }

    /**
     * @return string|null mensaje de error técnico, o null si se envió
     */
    public static function send(string $subject, string $text, string $replyEmail, string $replyName): ?string
    {
        $to = self::recipient();
        $host = (string) env('MAIL_HOST', '');
        if ($to === '' || $host === '') {
            return 'Correo no configurado (MAIL_HOST / MAIL_TO).';
        }
        $m = new PHPMailer(true);
        try {
            $m->isSMTP();
            $m->Host = $host;
            $m->Port = (int) env('MAIL_PORT', '587');
            $user = (string) env('MAIL_USER', '');
            $m->SMTPAuth = $user !== '';
            $m->Username = $user;
            $m->Password = (string) env('MAIL_PASS', '');
            $sec = strtolower((string) env('MAIL_SECURE', 'tls'));
            $m->SMTPSecure = $sec === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : ($sec === 'tls' ? PHPMailer::ENCRYPTION_STARTTLS : '');
            $m->SMTPAutoTLS = $sec !== 'none';
            $m->Timeout = 12;
            $m->CharSet = 'UTF-8';
            $from = (string) env('MAIL_FROM', $user);
            $m->setFrom($from, (string) env('MAIL_FROM_NAME', 'Sitio web'));
            foreach (preg_split('/[,;]\s*/', $to) ?: [] as $addr) {
                if (filter_var($addr, FILTER_VALIDATE_EMAIL)) {
                    $m->addAddress($addr);
                }
            }
            $m->addReplyTo($replyEmail, $replyName);
            $m->Subject = $subject;
            $m->Body = $text;
            $m->isHTML(false);
            $m->send();
            return null;
        } catch (MailException $e) {
            return $m->ErrorInfo ?: $e->getMessage();
        } catch (\Throwable $e) {
            return $e->getMessage();
        }
    }
}
