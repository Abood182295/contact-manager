<?php
declare(strict_types=1);

/**
 * Create the single admin user from the command line.
 *
 *   php scripts/create_admin.php admin
 *
 * The password is typed at a prompt (never passed as an argument, so it does not
 * end up in shell history) and is stored only as a password_hash() value.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('This script can only be run from the command line.');
}

define('BASE_PATH', dirname(__DIR__));
require BASE_PATH . '/core/bootstrap.php';

$username = $argv[1] ?? '';
if (!preg_match('/^[A-Za-z0-9_.-]{3,50}$/', $username)) {
    fwrite(STDERR, "Usage: php scripts/create_admin.php <username>\n");
    fwrite(STDERR, "Username: 3-50 characters (letters, digits, _ . -).\n");
    exit(1);
}

function prompt(string $label, bool $hidden): string
{
    fwrite(STDOUT, $label);
    $canHide = $hidden && DIRECTORY_SEPARATOR === '/' && function_exists('shell_exec');
    if ($canHide) {
        shell_exec('stty -echo 2>/dev/null');
    }
    $value = rtrim((string) fgets(STDIN), "\r\n");
    if ($canHide) {
        shell_exec('stty echo 2>/dev/null');
        fwrite(STDOUT, PHP_EOL);
    }
    return $value;
}

if (User::findByUsername($username) !== null) {
    fwrite(STDERR, "User '$username' already exists.\n");
    exit(1);
}

$password = prompt('Password (min 10 characters): ', true);
$confirm  = prompt('Repeat password: ', true);

if ($password !== $confirm) {
    fwrite(STDERR, "Passwords do not match.\n");
    exit(1);
}
if (mb_strlen($password) < 10) {
    fwrite(STDERR, "Password must be at least 10 characters.\n");
    exit(1);
}

User::create($username, $password);
fwrite(STDOUT, "Admin user '$username' created.\n");
