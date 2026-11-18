<?php
declare(strict_types=1);

/**
 * Admin user model. Passwords are stored only as password_hash() output.
 */
final class User
{
    public static function findByUsername(string $username): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT id, username, password_hash, failed_attempts, locked_until FROM users WHERE username = :u'
        );
        $stmt->execute(['u' => $username]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public static function create(string $username, string $password): int
    {
        $pdo  = Database::connection();
        $stmt = $pdo->prepare('INSERT INTO users (username, password_hash) VALUES (:u, :h)');
        $stmt->execute([
            'u' => $username,
            'h' => password_hash($password, PASSWORD_DEFAULT),
        ]);
        return (int) $pdo->lastInsertId();
    }

    public static function isLocked(array $user): bool
    {
        return $user['locked_until'] !== null && strtotime((string) $user['locked_until']) > time();
    }

    /** Count a failed login; lock the account for a while after too many failures. */
    public static function registerFailure(array $user, int $maxAttempts, int $lockMinutes): void
    {
        $attempts    = (int) $user['failed_attempts'] + 1;
        $lockedUntil = null;

        if ($attempts >= $maxAttempts) {
            $lockedUntil = gmdate('Y-m-d H:i:s', time() + $lockMinutes * 60);
            $attempts    = 0;
        }

        $stmt = Database::connection()->prepare(
            'UPDATE users SET failed_attempts = :a, locked_until = :l WHERE id = :id'
        );
        $stmt->execute(['a' => $attempts, 'l' => $lockedUntil, 'id' => $user['id']]);
    }

    public static function resetFailures(int $id): void
    {
        $stmt = Database::connection()->prepare(
            'UPDATE users SET failed_attempts = 0, locked_until = NULL WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
    }

    /** Upgrade the stored hash when PHP's default algorithm/cost has improved. */
    public static function rehashIfNeeded(int $id, string $password, string $currentHash): void
    {
        if (password_needs_rehash($currentHash, PASSWORD_DEFAULT)) {
            $stmt = Database::connection()->prepare('UPDATE users SET password_hash = :h WHERE id = :id');
            $stmt->execute(['h' => password_hash($password, PASSWORD_DEFAULT), 'id' => $id]);
        }
    }
}
