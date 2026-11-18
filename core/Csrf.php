<?php
declare(strict_types=1);

/**
 * CSRF protection: one random token per session, required on every POST.
 */
final class Csrf
{
    public static function token(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function field(): string
    {
        return '<input type="hidden" name="_csrf" value="' . e(self::token()) . '">';
    }

    public static function verify(mixed $submitted): bool
    {
        return is_string($submitted)
            && !empty($_SESSION['csrf_token'])
            && hash_equals($_SESSION['csrf_token'], $submitted);
    }
}
