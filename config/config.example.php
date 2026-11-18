<?php
/**
 * Copy this file to config/config.php and adjust the values.
 * config/config.php is listed in .gitignore so real credentials are never committed.
 */
return [
    'db' => [
        'dsn'  => 'mysql:host=127.0.0.1;dbname=contact_manager;charset=utf8mb4',
        'user' => 'cm_user',
        'pass' => 'change_me',
    ],

    // Keep false in production. true shows PHP errors on screen (development only).
    'debug' => false,

    // Log the admin out after this many seconds of inactivity.
    'session_timeout' => 1800,

    // Brute-force protection for the login form.
    'max_login_attempts' => 5,
    'lockout_minutes'    => 15,
];
