<?php
declare(strict_types=1);

/**
 * Escape a value for safe output inside HTML (text nodes and quoted attributes).
 * Every dynamic value printed in a view must go through this function.
 */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Build an internal URL for a route handled by the front controller.
 * Redirects and links are only ever built from fixed route names (no open redirects).
 */
function url(string $route, array $params = []): string
{
    return 'index.php?' . http_build_query(['r' => $route] + $params);
}
