<?php
declare(strict_types=1);

final class AuthController extends Controller
{
    // A real password_hash() value of a throwaway string. Used so that "unknown user" takes
    // about as long as "wrong password" (avoids username enumeration by timing).
    private const DUMMY_HASH = '$2y$10$0Gn9hZ9aHmEd2u5L82DUb.7ASf90IMA5b1qfBK/ARbJ.bViZakboi';

    public function showLogin(): void
    {
        if (Auth::check()) {
            $this->redirect('contacts');
        }
        View::render('auth/login', ['title' => 'Admin login', 'username' => '']);
    }

    public function login(): void
    {
        $this->requireCsrf();

        $username = $this->post('username');
        $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';

        $max  = (int) Config::get('max_login_attempts', 5);
        $mins = (int) Config::get('lockout_minutes', 15);

        $user = ($username !== '' && strlen($username) <= 50) ? User::findByUsername($username) : null;

        $ok = false;
        if ($user !== null) {
            if (User::isLocked($user)) {
                $this->fail($username, 'Too many failed attempts. Please try again later.', 429);
            }
            $ok = password_verify($password, $user['password_hash']);
        } else {
            password_verify($password, self::DUMMY_HASH);
        }

        if (!$ok) {
            if ($user !== null) {
                User::registerFailure($user, $max, $mins);
            }
            // Same message for "no such user" and "wrong password".
            $this->fail($username, 'Invalid username or password.', 401);
        }

        User::resetFailures((int) $user['id']);
        User::rehashIfNeeded((int) $user['id'], $password, $user['password_hash']);
        Auth::login($user);

        Flash::set('success', 'Welcome back, ' . $user['username'] . '.');
        $this->redirect('contacts');
    }

    public function logout(): void
    {
        $this->requireCsrf();
        Auth::logout();
        Flash::set('success', 'You have been logged out.');
        $this->redirect('contacts');
    }

    private function fail(string $username, string $message, int $status): never
    {
        http_response_code($status);
        Flash::set('error', $message);
        View::render('auth/login', ['title' => 'Admin login', 'username' => $username]);
        exit;
    }
}
