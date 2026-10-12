<?php
declare(strict_types=1);

namespace App\Core;

/** Textos y datos editables desde el panel (tabla settings). */
final class Settings
{
    private static ?array $cache = null;

    private static function load(): void
    {
        if (self::$cache !== null) {
            return;
        }
        self::$cache = [];
        try {
            foreach (Db::all('SELECT k, v, is_draft FROM settings') as $r) {
                self::$cache[$r['k']] = ['v' => (string) $r['v'], 'd' => (int) $r['is_draft']];
            }
        } catch (\Throwable $e) {
            error_log('Settings: ' . $e->getMessage());
        }
    }

    public static function get(string $key, string $default = ''): string
    {
        self::load();
        return self::$cache[$key]['v'] ?? $default;
    }

    public static function isDraft(string $key): bool
    {
        self::load();
        return ($self = self::$cache[$key] ?? null) !== null && $self['d'] === 1 && trim($self['v']) !== '';
    }

    public static function all(): array
    {
        self::load();
        return self::$cache;
    }

    /** Guarda un valor y lo marca como confirmado (ya no es provisional). */
    public static function set(string $key, string $value): void
    {
        self::save($key, $value, true);
    }

    /**
     * Guarda un valor. El texto deja de ser provisional si cambió o si se pide confirmarlo.
     */
    public static function save(string $key, string $value, bool $confirm = false): void
    {
        Db::exec(
            'INSERT INTO settings (k, v, is_draft) VALUES (?, ?, 0)
             ON DUPLICATE KEY UPDATE
               is_draft = IF(v <> VALUES(v) OR ? = 1, 0, is_draft),
               v = VALUES(v)',
            [$key, $value, $confirm ? 1 : 0]
        );
        self::$cache = null;
    }
}
