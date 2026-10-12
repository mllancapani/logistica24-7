<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Http;
use App\Core\Session;
use App\Core\View;

final class AuthController
{
    public static function form(): void
    {
        if (Auth::user()) {
            Http::redirect('/admin');
        }
        header('Cache-Control: no-store');
        View::render('admin/login', ['error' => null, 'email' => ''], 'admin/bare');
    }

    public static function login(): void
    {
        Session::start();
        Csrf::check();
        $email = (string) ($_POST['email'] ?? '');
        $err = Auth::attempt($email, (string) ($_POST['password'] ?? ''));
        if ($err === null) {
            Http::redirect('/admin');
        }
        http_response_code(401);
        View::render('admin/login', ['error' => $err, 'email' => $email], 'admin/bare');
    }

    public static function logout(): void
    {
        Session::start();
        Csrf::check();
        Auth::logout();
        Http::redirect('/admin/login');
    }
}
