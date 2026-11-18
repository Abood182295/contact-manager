<?php
declare(strict_types=1);

/**
 * Session-based authentication helpers.
 */
final class Auth
{
    public static function check(): bool
    {
        if (empty($_SESSION['user_id'])) {
            return false;
        }

        $timeout = (int) Config::get('session_timeout', 1800);
        if (time() - (int) ($_SESSION['last_activity'] ?? 0) > $timeout) {
            self::logout();
            return false;
        }

        $_SESSION['last_activity'] = time();
        return true;
    }

    public static function username(): string
    {
        return (string) ($_SESSION['username'] ?? '');
    }

    public static function login(array $user): void
    {
        // New session ID after login prevents session fixation.
        session_regenerate_id(true);
        $_SESSION['user_id']       = (int) $user['id'];
        $_SESSION['username']      = (string) $user['username'];
        $_SESSION['last_activity'] = time();
    }

    public static function logout(): void
    {
        $_SESSION = [];
        session_regenerate_id(true);
    }
}
