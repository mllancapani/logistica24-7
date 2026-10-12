<?php
declare(strict_types=1);

namespace App\Core;

final class Router
{
    /** @var array<int,array{0:string,1:string,2:callable}> */
    private array $routes = [];

    public function add(string $method, string $pattern, callable $handler): void
    {
        $this->routes[] = [$method, '#^' . $pattern . '$#', $handler];
    }

    public function get(string $p, callable $h): void { $this->add('GET', $p, $h); }
    public function post(string $p, callable $h): void { $this->add('POST', $p, $h); }

    public function dispatch(string $method, string $path): void
    {
        $path = '/' . trim($path, '/');
        $pathAllowed = false;
        foreach ($this->routes as [$m, $re, $h]) {
            if (preg_match($re, $path, $mt)) {
                $pathAllowed = true;
                if ($m === $method || ($m === 'GET' && $method === 'HEAD')) {
                    array_shift($mt);
                    $h(...array_map('rawurldecode', $mt));
                    return;
                }
            }
        }
        Http::abort($pathAllowed ? 405 : 404);
    }
}
