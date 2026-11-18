<?php
declare(strict_types=1);

/**
 * Tiny read-only configuration holder.
 */
final class Config
{
    private static array $items = [];

    public static function load(array $items): void
    {
        self::$items = $items;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return self::$items[$key] ?? $default;
    }
}
