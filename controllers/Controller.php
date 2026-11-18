<?php
declare(strict_types=1);

/**
 * Base controller with the shared security helpers.
 */
abstract class Controller
{
    /** Only logged-in users may continue; everyone else is sent to the login page. */
    protected function requireAuth(): void
    {
        if (!Auth::check()) {
            Flash::set('error', 'Please log in to continue.');
            $this->redirect('login');
        }
    }

    /** Reject any POST that does not carry the session's CSRF token. */
    protected function requireCsrf(): void
    {
        if (!Csrf::verify($_POST['_csrf'] ?? null)) {
            http_response_code(403);
            View::render('errors/403', ['title' => 'Forbidden']);
            exit;
        }
    }

    protected function redirect(string $route, array $params = []): never
    {
        header('Location: ' . url($route, $params));
        exit;
    }

    protected function notFound(): never
    {
        http_response_code(404);
        View::render('errors/404', ['title' => 'Not found']);
        exit;
    }

    /** Positive integer from a request array, or null if missing/invalid. */
    protected function intParam(array $source, string $key): ?int
    {
        $value = filter_var($source[$key] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        return $value === false ? null : $value;
    }

    /** Trimmed string from POST (arrays and other types become an empty string). */
    protected function post(string $key): string
    {
        $value = $_POST[$key] ?? '';
        return is_string($value) ? trim($value) : '';
    }
}
