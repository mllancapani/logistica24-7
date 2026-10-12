<?php
declare(strict_types=1);

namespace App\Core;

final class View
{
    /** Renderiza una vista dentro de un layout. $data queda disponible como variables. */
    public static function render(string $view, array $data = [], string $layout = 'public/layout'): void
    {
        $content = self::capture($view, $data);
        echo self::capture($layout, $data + ['content' => $content]);
    }

    public static function capture(string $view, array $data = []): string
    {
        $file = APP_ROOT . '/app/Views/' . $view . '.php';
        if (!is_file($file)) {
            throw new \RuntimeException("Vista no encontrada: $view");
        }
        extract($data, EXTR_SKIP);
        ob_start();
        include $file;
        return (string) ob_get_clean();
    }
}
