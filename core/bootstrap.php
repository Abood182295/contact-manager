<?php
declare(strict_types=1);

/**
 * Bootstrap: error handling, config, autoloading, hardened session, security headers.
 * BASE_PATH must be defined by the entry point (index.php or a CLI script).
 */

error_reporting(E_ALL);
ini_set('display_errors', '0'); // never leak stack traces / SQL errors to visitors
ini_set('log_errors', '1');
date_default_timezone_set('UTC');

require_once BASE_PATH . '/core/helpers.php';

// Minimal PSR-0 style autoloader limited to our own class folders.
spl_autoload_register(static function (string $class): void {
    if (!preg_match('/^[A-Za-z]+$/', $class)) {
        return;
    }
    foreach (['core', 'models', 'controllers'] as $dir) {
        $file = BASE_PATH . '/' . $dir . '/' . $class . '.php';
        if (is_file($file)) {
            require_once $file;
            return;
        }
    }
});

$configFile = BASE_PATH . '/config/config.php';
if (!is_file($configFile)) {
    http_response_code(500);
    exit('Application is not configured. Copy config/config.example.php to config/config.php.');
}
Config::load(require $configFile);

if (Config::get('debug', false) === true) {
    ini_set('display_errors', '1'); // development only
}

if (PHP_SAPI === 'cli') {
    return; // no session / headers for command-line scripts
}

// ---- Hardened session -------------------------------------------------------
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');

ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
session_name('cm_session');
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'secure'   => $isHttps,
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

// ---- Security headers -------------------------------------------------------
header_remove('X-Powered-By');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: no-referrer');
header("Content-Security-Policy: default-src 'self'; style-src 'self'; form-action 'self'; frame-ancestors 'none'; base-uri 'none'");
header('Cache-Control: no-store');
