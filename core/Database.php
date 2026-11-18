<?php
declare(strict_types=1);

/**
 * Single PDO connection shared by all models.
 *
 * - Exceptions on error (never silently continue after a failed query)
 * - Emulated prepares OFF so the database driver does real parameter binding
 */
final class Database
{
    private static ?PDO $pdo = null;

    public static function connection(): PDO
    {
        if (self::$pdo === null) {
            $db = Config::get('db');
            self::$pdo = new PDO($db['dsn'], $db['user'] ?? null, $db['pass'] ?? null, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        }
        return self::$pdo;
    }
}
