<?php
declare(strict_types=1);

/**
 * Renders a view inside the shared layout.
 * View names are always hard-coded by controllers, never taken from user input.
 */
final class View
{
    public static function render(string $view, array $data = []): void
    {
        $title = $data['title'] ?? 'Contact Manager';
        extract($data, EXTR_SKIP);

        ob_start();
        require BASE_PATH . '/views/' . $view . '.php';
        $content = ob_get_clean();

        require BASE_PATH . '/views/layout.php';
    }
}
