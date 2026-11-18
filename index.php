<?php
declare(strict_types=1);

/**
 * Front controller: every request enters here and is dispatched to a controller.
 * Only routes listed in the whitelist below can be reached; anything else is a 404.
 */

define('BASE_PATH', __DIR__);
require BASE_PATH . '/core/bootstrap.php';

// Route whitelist: HTTP method => route name => [controller class, action].
// Anything that changes data is POST-only (and CSRF-checked in the controller).
$routes = [
    'GET' => [
        'contacts'        => [ContactController::class, 'index'],
        'contacts/create' => [ContactController::class, 'create'],
        'contacts/edit'   => [ContactController::class, 'edit'],
        'contacts/delete' => [ContactController::class, 'confirmDelete'],
        'login'           => [AuthController::class, 'showLogin'],
    ],
    'POST' => [
        'contacts/store'  => [ContactController::class, 'store'],
        'contacts/update' => [ContactController::class, 'update'],
        'contacts/delete' => [ContactController::class, 'delete'],
        'login'           => [AuthController::class, 'login'],
        'logout'          => [AuthController::class, 'logout'],
    ],
];

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$route  = $_GET['r'] ?? 'contacts';
$route  = is_string($route) ? $route : '';

if (!isset($routes[$method][$route])) {
    http_response_code(404);
    View::render('errors/404', ['title' => 'Not found']);
    exit;
}

[$class, $action] = $routes[$method][$route];

try {
    (new $class())->$action();
} catch (Throwable $e) {
    // Log the details for the developer; show the visitor a generic page only.
    error_log('[contact-manager] ' . $e::class . ': ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    http_response_code(500);
    View::render('errors/500', ['title' => 'Server error']);
}
